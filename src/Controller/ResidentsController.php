<?php

namespace App\Controller;

use App\Entity\News;
use App\Repository\ResidentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ResidentsController extends AbstractController
{
    #[Route('/residents', name: 'app_residents')]
    public function index( ResidentsRepository $ResidentsRepository,EntityManagerInterface $entityManager ): Response
    {

        $residents = $ResidentsRepository->findAll();
        $news = $entityManager->getRepository(News::class)->findBy([], ['date' => 'DESC']);

        return $this->render('residents/index.html.twig', [
            'controller_name' => 'ResidentsController',
            'residents' => $residents,
            'show_carousel'=> true,
            'news' => $news,
        ]);
    }
}
