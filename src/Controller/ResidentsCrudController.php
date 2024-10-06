<?php

namespace App\Controller;

use App\Entity\ResidentInformation;
use App\Entity\Residents;
use App\Form\ResidentInformationType;
use App\Form\ResidentsType;
use App\Repository\ResidentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/residents/crud')]
class ResidentsCrudController extends AbstractController
{
    #[Route('/', name: 'app_residents_crud_index', methods: ['GET'])]
    #[IsGranted("ROLE_ADMIN")]
    public function index(ResidentsRepository $residentsRepository): Response
    {
        return $this->render('residents_crud/index.html.twig', [
            'residents' => $residentsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_residents_crud_new', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $resident = new Residents();
        $residentInformation = new ResidentInformation();
        $form = $this->createForm(ResidentsType::class, $resident);
        $form2 = $this->createForm(ResidentInformationType::class, $residentInformation);
        $form->handleRequest($request);
        $form2->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && ($form2->isSubmitted() && $form2->isValid())) {

            $residentInformation->setResident($resident);
            $entityManager->persist($resident);
            $entityManager->persist($residentInformation);
            $entityManager->flush();

            return $this->redirectToRoute('app_residents_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('residents_crud/new.html.twig', [
            'resident' => $resident,
            'residentInformation' => $residentInformation,
            'form' => $form,
            'form2' => $form2
        ]);
    }

    #[Route('/{id}', name: 'app_residents_crud_show', methods: ['GET'])]
    #[IsGranted("ROLE_ADMIN")]
    public function show(Residents $resident, ResidentInformation $residentInformation): Response
    {
        return $this->render('residents_crud/show.html.twig', [
            'resident' => $resident,
            'residentInformation' => $residentInformation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_residents_crud_edit', methods: ['GET', 'POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function edit(Request $request, Residents $resident, EntityManagerInterface $entityManager, ResidentInformation $residentInformation): Response
    {
        $form = $this->createForm(ResidentsType::class, $resident);
        $form2 = $this->createForm(ResidentInformationType::class, $residentInformation);
        $form->handleRequest($request);
        $form2->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && ($form2->isSubmitted() && $form2->isValid())) {

            $residentInformation->setResident($resident);
            $entityManager->flush();

            return $this->redirectToRoute('app_residents_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('residents_crud/edit.html.twig', [
            'resident' => $resident,
            'residentInformation' => $residentInformation,
            'form' => $form,
            'form2' => $form2
        ]);
    }

    #[Route('/{id}', name: 'app_residents_crud_delete', methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function delete(Request $request, Residents $resident, EntityManagerInterface $entityManager, ResidentInformation $residentInformation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$resident->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($resident);
            $entityManager->remove($residentInformation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_residents_crud_index', [], Response::HTTP_SEE_OTHER);
    }
}
