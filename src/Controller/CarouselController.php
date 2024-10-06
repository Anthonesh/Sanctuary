<?php

namespace App\Controller;

use App\Repository\ResidentsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CarouselController extends AbstractController
{
    #[Route('/components/carousel', name: 'app_carousel')]
    #[IsGranted("ROLE_ADMIN")]
    public function index(ResidentsRepository $residentsRepository): Response
    {
        $residents = $residentsRepository->findAll();

        return $this->render('components/carousel.html.twig', [
            'controller_name' => 'CarouselController',
            'residents' => $residents,
        ]);
    }
}
