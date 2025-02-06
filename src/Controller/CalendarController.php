<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\SeanceEnregistrement;
use App\Form\ReservationType;
use App\Service\TimeSlotService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class CalendarController extends AbstractController
{
    // La méthode pour obtenir les créneaux horaires disponibles
    private function getAvailableTimeSlots(int $duree): array
    {
        $timeSlots = [
            1 => [
                '12:30-13:30' => ['start' => '12:30', 'end' => '13:30'],
                '13:30-14:30' => ['start' => '13:30', 'end' => '14:30'],
                '14:30-15:30' => ['start' => '14:30', 'end' => '15:30'],
                '15:30-16:30' => ['start' => '15:30', 'end' => '16:30'],
                '16:30-17:30' => ['start' => '16:30', 'end' => '17:30'],
                '17:30-18:30' => ['start' => '17:30', 'end' => '18:30'],
                '18:30-19:30' => ['start' => '18:30', 'end' => '19:30'],
                '19:30-20:30' => ['start' => '19:30', 'end' => '20:30'],
                '20:30-21:30' => ['start' => '20:30', 'end' => '21:30'],
            ],
            2 => [
                '12:30-14:30' => ['start' => '12:30', 'end' => '14:30'],
                '14:30-16:30' => ['start' => '14:30', 'end' => '16:30'],
                '16:30-18:30' => ['start' => '16:30', 'end' => '18:30'],
                '18:30-20:30' => ['start' => '18:30', 'end' => '20:30'],
            ],
            3 => [
                '12:30-15:30' => ['start' => '12:30', 'end' => '15:30'],
                '15:30-18:30' => ['start' => '15:30', 'end' => '18:30'],
                '18:30-21:30' => ['start' => '18:30', 'end' => '21:30'],
            ],
            4 => [
                '12:30-16:30' => ['start' => '12:30', 'end' => '16:30'],
                '16:30-20:30' => ['start' => '16:30', 'end' => '20:30'],
            ],
            5 => [
                '12:30-17:30' => ['start' => '12:30', 'end' => '17:30'],
                '13:30-18:30' => ['start' => '13:30', 'end' => '18:30'],
                '16:30-21:30' => ['start' => '16:30', 'end' => '21:30'],
            ],
        ];

        return $timeSlots[$duree] ?? [];
    }

    #[Route('/enregistrement/{duree}/calendar', name: 'enregistrement_calendar')]
    public function calendar($duree, Request $request, EntityManagerInterface $entityManager): Response
    {
        $seance = new SeanceEnregistrement();
        $form = $this->createForm(ReservationType::class, $seance, ['duree' => $duree]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer l'heure de début sélectionnée
            $timeSlotString = $form->get('heure_debut')->getData();

            // Récupérer les créneaux horaires disponibles pour la durée
            $availableSlots = $this->getAvailableTimeSlots($duree);

            // Debugging: Afficher les créneaux horaires et l'heure sélectionnée
            dump($availableSlots);
            dump($timeSlotString);

            // Vérifier si le créneau horaire sélectionné fait partie des créneaux disponibles
            $foundSlot = false;
            foreach ($availableSlots as $slot => $times) {
                // Si l'heure de début correspond à une plage horaire, on marque qu'on l'a trouvée
                if (strpos($slot, $timeSlotString) === 0) {
                    $foundSlot = true;
                    $seance->setHeureDebut(new \DateTime($times['start']));
                    $seance->setHeureFin(new \DateTime($times['end']));
                    break; // On sort de la boucle si le créneau est trouvé
                }
            }

            if (!$foundSlot) {
                throw new \Exception("Le créneau horaire sélectionné est invalide.");
            }

            // Si un créneau valide est trouvé, on définit le tarif et on sauvegarde la séance
            $seance->setTarif($duree * 20);
            $entityManager->persist($seance);
            $entityManager->flush();

            return $this->redirectToRoute('reservation_confirmation');
        }

        return $this->render('calendar/index.html.twig', [
            'form' => $form->createView(),
            'duree' => $duree,
            'success' => 'Votre réservation a bien été enregistrée !', // Message de succès
        ]);
    }



/* Route pour la confirmation de la réservation  */

    #[Route('/confirmation', name: 'reservation_confirmation')]
public function confirmation(): Response
{
    return $this->render('calendar/confirmation.html.twig');
}
}



