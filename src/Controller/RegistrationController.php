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
            // 1. Gestion du Rôle (Récupéré depuis le champ non-mappé du formulaire)
            $selectedRole = $form->get('roles')->getData();
            if ($selectedRole) {
                $user->setRoles([$selectedRole]);
            }

            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // 2. Hashage du mot de passe
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // 3. Sauvegarde
            $entityManager->persist($user);
            $entityManager->flush();

            // 4. Message flash de succès (Optionnel mais recommandé pour le test)
            $this->addFlash('success', 'Inscription réussie ! Vous êtes connecté.');

            // 5. Connexion automatique et redirection
            return $security->login($user, AppAuthenticator::class, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
