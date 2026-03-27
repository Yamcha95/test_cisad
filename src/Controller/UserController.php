<?php

namespace App\Controller;

use App\Entity\Infos;
use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user')]
final class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // 1. Création d'un formulaire d'upload rapide
        $form = $this->createFormBuilder()
            ->add('file', FileType::class, ['label' => 'Fichier CSV (username, email, password)'])
            ->add('import', SubmitType::class, ['label' => 'Importer', 'attr' => ['class' => 'btn btn-success mt-2']])
            ->getForm();

        $form->handleRequest($request);

        // 2. Traitement du fichier si soumis
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();

            if ($file) {
                $handle = fopen($file->getRealPath(), 'r');
                $header = true;

                while (($data = fgetcsv($handle, 1000, ';')) !== false) {
                    if ($header) {
                        $header = false;
                        continue;
                    } // On saute la ligne d'entête

                    // On crée l'utilisateur ($data[0] = username, $data[1] = email, $data[2] = password)
                    $user = new User();
                    $user->setUserName($data[0]);
                    $user->setEmail($data[1]);
                    $user->setRoles(['ROLE_USER']);

                    // Hashage du mot de passe
                    $hashedPassword = $passwordHasher->hashPassword($user, $data[2]);
                    $user->setPassword($hashedPassword);

                    $entityManager->persist($user);
                }
                fclose($handle);
                $entityManager->flush();

                $this->addFlash('success', 'Importation réussie !');

                return $this->redirectToRoute('app_user_index');
            }
        }

        return $this->render('user/index.html.twig', [
            'users' => $entityManager->getRepository(User::class)->findAll(),
            'importForm' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $infos = new Infos();
            $infos->setUser($user);
            $infos->setRang('Bronze'); // Rang par défaut
            $infos->setVictoire('0');
            $infos->setDefaite('0');

            $entityManager->persist($infos);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
