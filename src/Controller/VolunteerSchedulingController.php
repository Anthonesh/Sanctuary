<?php

namespace App\Controller;

use App\Entity\News;
use App\Entity\Volunteer;
use App\Form\VolunteerEventType;
use App\Form\VolunteerType;
use App\Repository\CalendarRepository;
use App\Repository\ResidentsRepository;
use App\Service\ReservationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class VolunteerSchedulingController extends AbstractController
{
    #[Route('/volunteer/scheduling', name: 'app_volunteer_scheduling')]
    public function index(ResidentsRepository $residentsRepo,CalendarRepository $calendarRepo,EntityManagerInterface $entityManager
    ): Response {
        
        //récupère les informations évènements depuis le répository
        $events = $calendarRepo->findAll();
        //Déclare un tableau évènements vide
        $evts = [];
        // Transforme les données des événements pour les rendre compatibles avec l'affichage
        foreach ($events as $event) {
        
            // Stockez les informations de l'événement avec les informations des formulaires
            $evts[] = [
                'id' => $event->getId(),
                'title' => htmlspecialchars($event->getTitle()),
                'startTime' => $event->getStartTime()->format('Y-m-d H:i:s'),
                'endTime' => $event->getEndTime()->format('Y-m-d H:i:s'),
                'description' => $event->getDescription(),
                'volunteerPlaces' => $event->getVolunteerPlaces(),
            ];
        }

        $residents = $residentsRepo->findAll();
        $news = $entityManager->getRepository(News::class)->findBy([], ['date' => 'DESC']);


        return $this->render('event_planning/index.html.twig', [
            'controller_name' => 'EventPlanningController',
            'events' => $evts,
            'residents' => $residents,
            'show_carousel'=> true,
            'news' => $news,
        ]);
    }

    #[Route('/volunteer/reserver/{id}', name: 'app_volunteer_reserver')]
    public function reserver(Request $request,ResidentsRepository $residentsRepo,CalendarRepository $calendarRepo,
    ReservationService $reservationService,EntityManagerInterface $entityManager, $id): Response {

        // Trouve l'événement à réserver par son ID
        $calendar = $calendarRepo->find($id);

        // Crée une nouvelle réservation
        $volunteer = new Volunteer();
        $volunteer->setCalendar($calendar);
        // Si l'utilisateur est connecté, pré-remplissez le formulaire avec ses informations
        $user = $this->getUser();
        if ($user) {
            $volunteer->setUser($user);
        }
        $form = $this->createForm(VolunteerType::class, $volunteer);
        $form->handleRequest($request);
    
        // Vérifie si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Tente d'effectuer la réservation
                $reservationService->volunteerReservation($calendar, $volunteer);
                // Message de succès
                $this->addFlash('success', 'Réservation effectuée avec succès.');
            } catch (\Exception $e) {
                // Gère les erreurs éventuelles
                $this->addFlash('error', $e->getMessage());
            }
            // Redirige vers la page principale de l'événementiel
            return $this->redirectToRoute('app_volunteer_scheduling');
        }
        $residents = $residentsRepo->findAll();
        $news = $entityManager->getRepository(News::class)->findBy([], ['date' => 'DESC']);

        return $this->render('calendar/new.html.twig', [
            'form' => $form->createView(),
            'residents' => $residents,
            'show_carousel'=> true,
            'news' => $news,
        ]);
    }
}