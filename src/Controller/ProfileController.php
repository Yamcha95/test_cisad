<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')] // C'est cette ligne qui répare l'erreur !
    public function index(): Response
    {
        // On récupère l'utilisateur connecté
        $user = $this->getUser();

        // Si l'utilisateur n'est pas connecté, on le redirige (sécurité)
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('profile/index.html.twig', [
            'user' => $user,
        ]);
    }
}
