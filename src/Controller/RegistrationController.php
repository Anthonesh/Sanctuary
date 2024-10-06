<?php

namespace App\Controller;

use App\Entity\News;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Service\ApprovalEmailSender;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private $emailVerifier;
    private $approvalEmailSender;

    public function __construct(EmailVerifier $emailVerifier, ApprovalEmailSender $approvalEmailSender)
    {
        $this->emailVerifier = $emailVerifier;
        $this->approvalEmailSender = $approvalEmailSender;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Check if the user has chosen the volunteer role
            if ($form->get('roles')->getData() === 'ROLE_VOLUNTEER') {
                // Set the user's role to volunteer but inactive pending admin approval
                $user->setRoles(['ROLE_VOLUNTEER_PENDING']);
                $user->setVerified(false); // or any other field you use to indicate pending status
            } else {
                // Set the user's role to user if not registering as a volunteer
                $user->setRoles(['ROLE_USER']);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            // Generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('refugestbres@gmail.com', 'Refuge Mail Confirmation'))
                    ->to($user->getEmail())
                    ->subject('Confirmez votre email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            return $this->redirectToRoute('app_home');
        }
        $news = $entityManager->getRepository(News::class)->findBy([], ['date' => 'DESC']);

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'show_carousel' => false,
            'news' => $news
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    #[IsGranted("ROLE_ADMIN")]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository, MailerInterface $mailer): Response
    {
        $id = $request->query->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // If the user is a pending volunteer, send an email to the admin for approval
        if (in_array('ROLE_USER', $user->getRoles())) {
            $this->approvalEmailSender->sendApprovalEmail($user);
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_home');
    }
}
