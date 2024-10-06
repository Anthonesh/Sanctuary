<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Entity\News;
use App\Form\CalendarType;
use App\Repository\CalendarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/calendar/crud')]
class CalendarCrudController extends AbstractController
{
    #[Route('/', name: 'app_calendar_crud_index', methods: ['GET'])]
    #[IsGranted("ROLE_ADMIN")]

    public function index(CalendarRepository $calendarRepository): Response
    {
        return $this->render('calendar_crud/index.html.twig', [
            'calendars' => $calendarRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_calendar_crud_new', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]

    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $calendar = new Calendar();
        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($calendar);
            $entityManager->flush();

            return $this->redirectToRoute('app_calendar_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        $news = $entityManager->getRepository(News::class)->findBy([], ['date' => 'DESC']);

        return $this->render('calendar_crud/new.html.twig', [
            'calendar' => $calendar,
            'form' => $form,
            'news' => $news,
            'show_carousel' => false
        ]);
    }

    #[Route('/{id}', name: 'app_calendar_crud_show', methods: ['GET'])]
    #[IsGranted("ROLE_ADMIN")]

    public function show(Calendar $calendar): Response
    {
        return $this->render('calendar_crud/show.html.twig', [
            'calendar' => $calendar,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_calendar_crud_edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]

    public function edit(Request $request, Calendar $calendar, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_calendar_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('calendar_crud/edit.html.twig', [
            'calendar' => $calendar,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_calendar_crud_delete', methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]

    public function delete(Request $request, Calendar $calendar, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$calendar->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($calendar);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_calendar_crud_index', [], Response::HTTP_SEE_OTHER);
    }
}
