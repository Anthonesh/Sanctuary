<?php

namespace App\Controller;

use App\Entity\News;
use App\Entity\ResidentInformation;
use App\Entity\Residents;
use App\Repository\ResidentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ResidentsDetailsController extends AbstractController
{
    #[Route('/residents/details/{id}', name: 'app_residents_details')]
    public function index(HttpFoundationRequest $request,EntityManagerInterface $entityManager, ResidentsRepository $residentsRepository,  $id): Response
    {
        $resident = $entityManager->getRepository(Residents::class)->find($id);
        $residents = $residentsRepository->findAll();

        if (!$resident) {
            throw $this->createNotFoundException("Le pensionnaire n'a pas été trouvé.");
        }

        $residentInformation = $entityManager->getRepository(ResidentInformation::class)->find($id);
        $news = $entityManager->getRepository(News::class)->findBy([], ['date' => 'DESC']);


        return $this->render('residents_details/index.html.twig', [
            'resident' => $resident,
            'residents' => $residents,
            'residentInformation' => $residentInformation,
            'show_carousel'=> true,
            'news' => $news,
        ]);
    }
}
