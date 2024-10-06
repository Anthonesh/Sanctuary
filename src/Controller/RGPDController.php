<?php

namespace App\Controller;

use App\Entity\News;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RGPDController extends AbstractController
{
    #[Route('/rgpd', name: 'app_rgpd')]
    public function index(EntityManagerInterface $entityManager): Response
    {

        $news = $entityManager->getRepository(News::class)->findBy([], ['date' => 'DESC']);

        return $this->render('rgpd/index.html.twig', [
            'controller_name' => 'RGPDController',
            'show_carousel'=> false,
            'news' => $news,
        ]);
    }
}
