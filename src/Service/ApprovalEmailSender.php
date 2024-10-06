<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport\TransportInterface;

class ApprovalEmailSender
{
    public function __construct(
        private MailerInterface $mailer, 
        private TransportInterface $adminMailer, 
        private EntityManagerInterface $entityManager
        ){
    }

    public function sendApprovalEmail(User $user): void
    {
        if (in_array('ROLE_USER', $user->getRoles())) {
            $adminEmail = 'refugestbres@gmail.com'; // Replace with the admin's email

            $email = (new TemplatedEmail())
                ->from($user->getEmail()) 
                ->to($adminEmail)
                ->subject('Nouvelle inscription en tant que bénévole')
                ->htmlTemplate('registration/admin_approval.html.twig')
                ->context(['user' => $user]); 

            $this->adminMailer->send($email); // Utilisation de adminMailer pour envoyer l'e-mail
        }
    }
}