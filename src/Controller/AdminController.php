<?php

namespace App\Controller;

use App\Entity\News;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    #[IsGranted("ROLE_ADMIN")]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $news = $entityManager->getRepository(News::class)->findBy([], ['date' => 'DESC']);

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'show_carousel' => false,
            'news' => $news,
        ]);
    }
}
