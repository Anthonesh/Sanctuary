<?php

namespace App\Service;

use App\Entity\Calendar;
use App\Entity\ReservationForm;
use App\Entity\Volunteer;
use Doctrine\ORM\EntityManagerInterface;

class ReservationService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function eventReservation(Calendar $calendar, ReservationForm $reservation)
    {
        // Ajoutez la réservation au calendrier
        $calendar->addForm($reservation);
    
        // Persister les modifications dans la base de données
        $this->entityManager->persist($reservation);
        $this->entityManager->flush();
    }

    public function volunteerReservation (calendar $calendar, Volunteer $volunteer)
{
    $calendar->addVolunteer($volunteer);

    $this->entityManager->persist($volunteer);
    $this->entityManager->flush();
    
}
}

