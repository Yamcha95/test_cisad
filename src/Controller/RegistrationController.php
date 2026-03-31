<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        Security $security,
        EntityManagerInterface $entityManager,
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le mot de passe en clair du formulaire
            $plainPassword = $form->get('plainPassword')->getData();
            
            // Récupérer le rôle choisi
            $role = $form->get('roles')->getData();
            $user->setRoles([$role]);

            // 1. Hasher le mot de passe
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // 2. CRÉER LES INFOS PAR DÉFAUT (RANG FER)
            $infos = new \App\Entity\Infos(); // N'oublie pas l'import en haut ou le chemin complet
            $infos->setUser($user);
            $infos->setRang('Fer'); // Ton nouveau standard systématique
            $infos->setVictoire(0);
            $infos->setDefaite(0);

            // 3. Sauvegarde tout
            $entityManager->persist($user);
            $entityManager->persist($infos); // On persiste aussi les infos !
            $entityManager->flush();

            $this->addFlash('success', 'Bienvenue ! Votre compte est prêt au rang Fer.');

            return $security->login($user, AppAuthenticator::class, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
