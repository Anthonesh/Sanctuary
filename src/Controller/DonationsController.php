<?php

namespace App\Controller;

use App\Entity\Donations;
use App\Entity\News;
use App\Form\DonationsFormType;
use App\Repository\ResidentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DonationsController extends AbstractController
{
    #[Route('/donations', name: 'app_donations', methods: ['GET', 'POST'])]
    public function submitForm(Request $request, EntityManagerInterface $entityManager, ResidentsRepository $residentsRepo): Response
    {
        // Création d'une nouvelle instance de l'entité Dons.
        $dons = new Donations();
        // Création du formulaire 
        $form = $this->createForm(DonationsFormType::class, $dons);
        // Requête pour remplir le formulaire avec les données envoyées par l'utilisateur.
        $form->handleRequest($request);
    
        // Vérification si le formulaire a été soumis et est valide.
        if ($form->isSubmitted() && $form->isValid()) {
  // Stocker les données du formulaire dans la session.
    $request->getSession()->set('donsFormData', $form->getData());
    
            // Redirection vers la page de paiement.
            return $this->redirectToRoute('app_payment');
        } else if ($form->isSubmitted() && !$form->isValid()) {
            // Si le formulaire a été soumis mais n'est pas valide, stocker un message d'erreur dans la session.
            $request->getSession()->getFlashBag()->add('error', 'Il y a des erreurs dans le formulaire, veuillez les corriger.');
        }
    
        $residents = $residentsRepo->findAll();
        $news = $entityManager->getRepository(News::class)->findBy([], ['date' => 'DESC']);
        // Si le formulaire n'est pas soumis ou n'est pas valide, afficher la page avec le formulaire.
        // Rendu du template 'dons/index.html.twig' en passant le formulaire à la vue pour être affiché.
        return $this->render('donations/index.html.twig', [
            'form' => $form->createView(),
            'residents' => $residents,
            'show_carousel'=> true,
            'news' => $news,
        ]);
    }
    #[Route('/donations/payment', name: 'app_payment', methods: ['GET'])]
    public function paiement(Request $request, EntityManagerInterface $entityManager, ResidentsRepository $residentsRepo): Response
    {
        // Récupérer les données du formulaire depuis la session
        $donsFormData = $request->getSession()->get('donsFormData');

        if (!$donsFormData) {
            // Gérer le cas où les données du formulaire ne sont pas trouvées dans la session
            return $this->redirectToRoute('app_dons');
        }

        // Configuration Stripe
        $stripePublicKey = $this->getParameter('stripe.public_key');
        $stripeSecretKey = $this->getParameter('stripe.secret_key');
        Stripe::setApiKey($stripeSecretKey);

        // Créer un PaymentIntent avec Stripe
        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $donsFormData->getAmount() * 100, // Montant en centimes
                'currency' => $donsFormData->getCurrency(),
                'payment_method_types' => ['card'],
                'show_carousel'=> false
            ]);

            $clientSecret = $paymentIntent->client_secret;
            $residents = $residentsRepo->findAll();

            return $this->render('donations/payment.html.twig', [
                'stripePublicKey' => $stripePublicKey,
                'clientSecret' => $clientSecret,
                'residents' => $residents
            ]);

        } catch (ApiErrorException $e) {
            // En cas d'erreur
            $this->addFlash('error', 'Erreur de paiement : ' . $e->getMessage());
            return $this->redirectToRoute('app_donations');
        }
    }

    #[Route('/donations/success', name: 'app_donations_success', methods: ['GET']) ]
    public function paiementSucces(Request $request, EntityManagerInterface $entityManager,ResidentsRepository $residentsRepo): Response
    
    {
        // Récupérer les données du formulaire depuis la session
        $donsFormData = $request->getSession()->get('donsFormData');

        if (!$donsFormData) {
            // Gérer le cas où les données du formulaire ne sont pas trouvées dans la session
            return $this->redirectToRoute('app_donations');
        }

        // Insérer les données dans la base de données
        $entityManager->persist($donsFormData);
        $entityManager->flush();

        // Supprimer les données de la session après les avoir insérées dans la base de données
        $request->getSession()->remove('donsFormData');

        $residents = $residentsRepo->findAll();
        $news = $entityManager->getRepository(News::class)->findBy([], ['date' => 'DESC']);
        // Afficher la page de succès
        return $this->render('donations/success.html.twig',[
            'residents' => $residents,
            'show_carousel'=> true,
            'news' => $news
        ]);
    }
}