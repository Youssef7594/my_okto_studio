<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EnregistrementController extends AbstractController
{
 
    #[Route('/enregistrement', name: 'app_enregistrement')]
    public function index(): Response
    {
        return $this->render('enregistrement/index.html.twig', [
            'controller_name' => 'EnregistrementController',
        ]);
    }

    #[Route('/enregistrement/{duree}', name: 'enregistrement_duree')]
    public function reserver($duree): Response
    {
        return $this->redirectToRoute('enregistrement_calendar', ['duree' => $duree]);
    }
}


