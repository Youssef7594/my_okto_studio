<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;



class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
       // Récupérer l'erreur de connexion (s'il y en a une)
       $error = $authenticationUtils->getLastAuthenticationError();

       // Récupérer le dernier nom d'utilisateur utilisé
       $lastUsername = $authenticationUtils->getLastUsername();

       return $this->render('security/login.html.twig', [
           'last_username' => $lastUsername,
           'error'         => $error,
       ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Symfony gère la déconnexion automatiquement
    }
}

