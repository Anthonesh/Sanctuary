<?php

namespace App\Controller;

use App\Entity\News;
use App\Form\NewsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class NewsController extends AbstractController
{
    #[Route('/news', name: 'app_news_index')]
    #[IsGranted("ROLE_ADMIN")]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $news = $entityManager->getRepository(News::class)->findBy([], ['date' => 'DESC']);

        return $this->render('news/index.html.twig', [
            'news' => $news,
            'show_carousel' => false
        ]);
    }

    #[Route('/news/ajouter', name: 'app_news_add')]
    #[IsGranted("ROLE_ADMIN")]    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $news = new News();
        $form = $this->createForm(NewsType::class, $news);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $news->setDate(new \DateTime('now', new \DateTimeZone('Europe/Paris')));            $entityManager->persist($news);
            $entityManager->flush();

            return $this->redirectToRoute('app_news_index'); // Redirect to the list of news
        }
        $newsList = $entityManager->getRepository(News::class)->findBy([], ['date' => 'DESC']);

        return $this->render('news/form.html.twig', [
            'form' => $form->createView(),
            'news' => $news,
            'newsList' => $newsList,
            'show_carousel' => false
        ]);
    }

    #[Route('/news/{id}', name: 'app_news_show')]
    public function show(News $news): Response
    {
        return $this->render('news/show.html.twig', [
            'news' => $news,
            'show_carousel' => false
        ]);
    }

    #[Route('/news/edit/{id}', name: 'app_news_edit')]
    public function edit(Request $request, News $news, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush(); // Pas besoin de persister à nouveau car l'objet $news est déjà géré

            return $this->redirectToRoute('app_news_index');
        }

        return $this->render('news/form.html.twig', [
            'form' => $form->createView(),
            'news' => $news,
            'show_carousel' => false
        ]);
    }

    #[Route('/news/delete/{id}', name: 'app_news_delete')]
    public function delete(News $news, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($news);
        $entityManager->flush();

        return $this->redirectToRoute('app_news_index'); // Redirect to the list of news
    }

}
