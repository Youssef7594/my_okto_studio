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
    /* Récupére les heure disponnible  */
    private function getBookedTimeSlots(EntityManagerInterface $entityManager, \DateTimeInterface $date): array
{
    $reservations = $entityManager->getRepository(SeanceEnregistrement::class)->findBy(['date' => $date]);

    $bookedSlots = [];

    foreach ($reservations as $reservation) {
        $start = $reservation->getHeureDebut()->format('H:i');
        $end = $reservation->getHeureFin()->format('H:i');
        $bookedSlots[] = "$start-$end";
    }

    return $bookedSlots;
}

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
    
    // Récupération des créneaux disponibles pour la durée choisie
    $availableSlots = $this->getAvailableTimeSlots($duree);

    // Création du formulaire
    $form = $this->createForm(ReservationType::class, $seance, [
        'duree' => $duree,
        'available_time_slots' => $availableSlots,
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // 🔥 Récupérer la date sélectionnée
        $date = $form->get('date')->getData();
        
        if (!$date) {
            throw new \Exception("Aucune date sélectionnée.");
        }

        // 🔥 Récupérer les créneaux déjà réservés pour cette date
        $bookedSlots = $this->getBookedTimeSlots($entityManager, $date);

        // 🔥 Filtrer les créneaux disponibles
        $filteredSlots = array_filter($availableSlots, function ($times) use ($bookedSlots) {
            return !in_array("{$times['start']}-{$times['end']}", $bookedSlots);
        });

        // 🔥 Vérifier si l'heure sélectionnée est encore dispo
        $timeSlotString = $form->get('heure_debut')->getData();
        if ($timeSlotString instanceof \DateTime) {
            $timeSlotString = $timeSlotString->format('H:i');
        }

        $foundSlot = false;
        foreach ($filteredSlots as $slot => $times) {
            if (strpos($slot, $timeSlotString) === 0) {
                $foundSlot = true;
                $seance->setDate($date);
                $seance->setHeureDebut(new \DateTime($times['start']));
                $seance->setHeureFin(new \DateTime($times['end']));
                break;
            }
        }

        if (!$foundSlot) {
            return $this->render('calendar/aucune_dispo.html.twig', [
                'date' => $date,
                'heure' => $timeSlotString ?? null,
            ]);
        }

        // 🔥 Enregistrement de la réservation
        $seance->setTarif($duree * 20);
        $entityManager->persist($seance);
        $entityManager->flush();

        return $this->redirectToRoute('reservation_confirmation');
    }

    return $this->render('calendar/index.html.twig', [
        'form' => $form->createView(),
        'duree' => $duree,
    ]);
}





/* Route pour la confirmation de la réservation  */

    #[Route('/confirmation', name: 'reservation_confirmation')]
public function confirmation(): Response
{
    return $this->render('calendar/confirmation.html.twig');
}
}



