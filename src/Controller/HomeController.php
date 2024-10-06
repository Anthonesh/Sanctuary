<?php

namespace App\Controller;

use App\Entity\News;
use App\Repository\ResidentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index( ResidentsRepository $ResidentsRepository, EntityManagerInterface $entityManager ): Response
    {

        $residents = $ResidentsRepository->findAll();
        $news = $entityManager->getRepository(News::class)->findBy([], ['date' => 'DESC']);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'residents' => $residents,
            'news' => $news,
            'show_carousel'=> true,
        ]);
    }

}
