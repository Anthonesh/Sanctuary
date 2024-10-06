<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Entity\News;
use App\Entity\Volunteer;
use App\Form\VolunteerType;
use App\Repository\CalendarRepository;
use App\Repository\ResidentsRepository;
use App\Repository\VolunteerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/volunteer/crud')]
class VolunteerCrudController extends AbstractController
{
    #[Route('/', name: 'app_volunteer_crud_index', methods: ['GET'])]
    #[IsGranted("ROLE_ADMIN")]
    public function index(VolunteerRepository $volunteerRepository): Response
    {
        return $this->render('volunteer_crud/index.html.twig', [
            'volunteers' => $volunteerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_volunteer_crud_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CalendarRepository $calendarRepo,ResidentsRepository $residentsRepo): Response
    {
        $volunteer = new Volunteer();
        $form = $this->createForm(VolunteerType::class, $volunteer);
        $form->handleRequest($request);
    
        $eventId = $request->query->get('eventId');
        $date = $request->query->get('date');
    
        if ($eventId) {
            $calendar = $calendarRepo->find($eventId);
            if ($calendar) {
                $volunteer->setCalendar($calendar);
            } else {
                $this->addFlash('error', 'Calendrier non trouvé.');
                return $this->redirectToRoute('app_volunteer_scheduling');
            }
        }
        $user = $this->getUser();
        if ($user) {
            $volunteer->setUser($user);
        }
    
        if ($form->isSubmitted() && $form->isValid()) {
            $calendar = $volunteer->getCalendar();
            if ($calendar) {
                $volunteers = $volunteer->getNumberOfVolunteers();
                $volunteerPlaces = $calendar->getVolunteerPlaces();

                if ($volunteerPlaces >= $volunteers) {
                    $startTimeData = $form->get('startTime')->getData();
                    $endTimeData = $form->get('endTime')->getData();
        
                    if ($startTimeData && $endTimeData) {
                        $startTime = new \DateTime($date . ' ' . $startTimeData->format('H:i:s'));
                        $endTime = new \DateTime($date . ' ' . $endTimeData->format('H:i:s'));
                        $volunteer->setStartTime($startTime);
                        $volunteer->setEndTime($endTime);
        
                        // Décrémentez volunteer_places au lieu de places
                        $calendar->setVolunteerPlaces($volunteerPlaces - $volunteers);
        
                        
                        $entityManager->persist($volunteer);
                        $entityManager->persist($calendar);
                        $entityManager->flush();
        
                        $this->addFlash('success', 'Votre réservation a été enregistrée avec succès.');
                        return $this->redirectToRoute('app_volunteer_scheduling');
                    } else {
                        $this->addFlash('error', 'Veuillez fournir les heures de début et de fin.');
                    }
                } else {
                    $this->addFlash('error', 'Il n’y a pas assez de places disponibles pour cette réservation.');
                }
            } else {
                $this->addFlash('error', 'Calendrier non associé.');
            }
        }
        $residents = $residentsRepo->findAll();
        $news = $entityManager->getRepository(News::class)->findBy([], ['date' => 'DESC']);

    
        return $this->render('volunteer_crud/new.html.twig', [
            'volunteer' => $volunteer,
            'form' => $form,
            'show_carousel'=> true,
            'residents' => $residents,
            'news' => $news,
        ]);
    }

    #[Route('/reserve', name: 'app_volunteer_crud_reserve', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function reserve(Request $request, EntityManagerInterface $entityManager, CalendarRepository $calendarRepo): Response
    {
        $volunteer = new Volunteer();
        $form = $this->createForm(VolunteerType::class, $volunteer);
        $form->handleRequest($request);
    
        $date = $request->query->get('date');

        $user = $this->getUser();
        if ($user) {
            $volunteer->setUser($user);
        }
        
        if ($form->isSubmitted() && $form->isValid()) {
            if ($date) {
                $startTimeData = $form->get('startTime')->getData();
                $endTimeData = $form->get('endTime')->getData();
    
                if ($startTimeData && $endTimeData) {
                    $startTime = new \DateTime($date . ' ' . $startTimeData->format('H:i:s'));
                    $endTime = new \DateTime($date . ' ' . $endTimeData->format('H:i:s'));
                    $volunteer->setStartTime($startTime);
                    $volunteer->setEndTime($endTime);
                } else {
                    // Gérer le cas où les heures ne sont pas fournies
                    $this->addFlash('error', 'Veuillez fournir les heures de début et de fin.');
                    return $this->redirectToRoute('app_volunteer_crud_reserve', ['date' => $date]);
                }
            }
    
            $entityManager->persist($volunteer);
            $entityManager->flush();
    
            $this->addFlash('success', 'Votre réservation a été enregistrée avec succès.');
            return $this->redirectToRoute('app_event_planning');
        }

        $news = $entityManager->getRepository(News::class)->findBy([], ['date' => 'DESC']);

    
        return $this->render('volunteer_crud/new.html.twig', [
            'volunteer' => $volunteer,
            'form' => $form,
            'show_carousel' => false,
            'news' => $news,
        ]);
    }

    #[Route('/{id}', name: 'app_volunteer_crud_show', methods: ['GET'])]
    #[IsGranted("ROLE_ADMIN")]
    public function show(Volunteer $volunteer): Response
    {
        return $this->render('volunteer_crud/show.html.twig', [
            'volunteer' => $volunteer,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_volunteer_crud_edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function edit(Request $request, Volunteer $volunteer, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VolunteerType::class, $volunteer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_volunteer_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('volunteer_crud/edit.html.twig', [
            'volunteer' => $volunteer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_volunteer_crud_delete', methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function delete(Request $request, Volunteer $volunteer, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$volunteer->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($volunteer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_volunteer_crud_index', [], Response::HTTP_SEE_OTHER);
    }
}
