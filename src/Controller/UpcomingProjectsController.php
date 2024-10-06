<?php

namespace App\Controller;

use App\Entity\News;
use App\Repository\ResidentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UpcomingProjectsController extends AbstractController
{
    #[Route('/upcoming/projects', name: 'app_upcoming_projects')]
    public function index(ResidentsRepository $residentsRepository,EntityManagerInterface $entityManager): Response
    {

        $residents = $residentsRepository->findAll();
        $news = $entityManager->getRepository(News::class)->findBy([], ['date' => 'DESC']);


        return $this->render('upcoming_projects/index.html.twig', [
            'controller_name' => 'UpcomingProjectsController',
            'residents' => $residents,
            'show_carousel'=> true,
            'news' => $news,
        ]);
    }
}
