<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\SeanceEnregistrement;
use Doctrine\ORM\EntityManagerInterface;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }


    #[Route('/Admin', name: 'app_admin')]
    public function admin(EntityManagerInterface $entityManager): Response
    {
        // Récupérer toutes les réservations
    $reservations = $entityManager->getRepository(SeanceEnregistrement::class)->findAll();

    return $this->render('home/admin.html.twig', [
        'reservations' => $reservations, // 🔥 Envoie les réservations au template
    ]);
    }


    
    /* Route pour suprimer une réservation */
    #[Route('/Admin/delete/{id}', name: 'app_admin_delete', methods: ['POST'])]
public function deleteReservation(int $id, EntityManagerInterface $entityManager): Response
{
    $reservation = $entityManager->getRepository(SeanceEnregistrement::class)->find($id);

    if ($reservation) {
        $entityManager->remove($reservation);
        $entityManager->flush();
        $this->addFlash('success', 'La réservation a été supprimée.');
    } else {
        $this->addFlash('error', 'La réservation n\'existe pas.');
    }

    return $this->redirectToRoute('app_admin'); // Redirige vers la page Admin après suppression
}
}
