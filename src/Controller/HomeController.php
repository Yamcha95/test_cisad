<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')] // La route est maintenant la racine
    public function index(): Response
    {
        // Petite logique sympa : si l'utilisateur est déjà connecté, 
        // on l'envoie direct sur son profil au lieu de l'accueil
        if ($this->getUser()) {
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('home/index.html.twig');
    }
}