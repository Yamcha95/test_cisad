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
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

#[Route('/user')]
final class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createFormBuilder()
            ->add('file', FileType::class, ['label' => 'Fichier CSV'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();

            if ($file) {
                try {
                    $handle = fopen($file->getRealPath(), 'r');
                    $header = true;

                    while (($data = fgetcsv($handle, 1000, ';')) !== false) {
                        if ($header) {
                            $header = false;
                            continue;
                        }

                        // 1. Création de l'User
                        $user = new User();
                        $user->setUsername($data[0]); // username
                        $user->setEmail($data[1]);    // email

                        // On gère le rôle depuis le CSV (data[3]), sinon ROLE_USER par défaut
                        $role = !empty($data[3]) ? $data[3] : 'ROLE_USER';
                        $user->setRoles([$role]);

                        $hashedPassword = $passwordHasher->hashPassword($user, $data[2]); // password
                        $user->setPassword($hashedPassword);

                        $entityManager->persist($user);

                        // 2. CRÉATION DES INFOS (C'est ce qui manquait !)
                        $infos = new Infos();
                        $infos->setRang(!empty($data[4]) ? $data[4] : 'Bronze'); // rank
                        $infos->setVictoire(!empty($data[5]) ? $data[5] : '0');   // victoire
                        $infos->setDefaite(!empty($data[6]) ? $data[6] : '0');    // defaite

                        // On lie l'objet Infos à l'User
                        $infos->setUser($user);

                        $entityManager->persist($infos);
                    }
                    fclose($handle);
                    $entityManager->flush();

                    $this->addFlash('success', 'Importation réussie avec les rangs et statistiques !');

                    return $this->redirectToRoute('app_user_index');
                } catch (UniqueConstraintViolationException $e) {
                    // On attrape l'erreur de doublon (Email déjà existant)
                    $this->addFlash('danger', "Erreur : Un ou plusieurs utilisateurs (emails) existent déjà dans la base.");
                    
                    // Optionnel : On peut vider l'EntityManager pour éviter de tenter de ré-enregistrer 
                    // des objets corrompus lors de la prochaine requête.
                    $entityManager->clear(); 

                } catch (\Exception $e) {
                    // On attrape toutes les autres erreurs imprévues
                    $this->addFlash('danger', "Une erreur inattendue est survenue lors de l'importation.");
                } finally {
                    if (is_resource($handle)) {
                        fclose($handle);
                    }
                }
            }
        }

        return $this->render('user/index.html.twig', [
            'users' => $entityManager->getRepository(User::class)->findAll(),
            'importForm' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // 1. RÉCUPÉRER LE MOT DE PASSE DU CHAMP NON-MAPPÉ
            $plainPassword = $form->get('plainPassword')->getData();

            // 2. HASHER ET ENREGISTRER DANS L'ENTITÉ
            if ($plainPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            $entityManager->persist($user);

            // Création auto des infos par défaut
            $infos = new Infos();
            $infos->setUser($user);
            $infos->setRang('Iron');
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
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
