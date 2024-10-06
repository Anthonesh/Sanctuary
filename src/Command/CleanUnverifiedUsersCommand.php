<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'clean:unverified-users',
    description: 'Delete unverified users',
)]
class CleanUnverifiedUsersCommand extends Command
{
    private $userRepository;
    private $entityManager;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $unverifiedUsers = $this->userRepository->findBy(['isVerified' => false]);

        foreach ($unverifiedUsers as $user) {
            $this->entityManager->remove($user);
        }

        $this->entityManager->flush();

        $io->success('Unverified users have been deleted.');

        return Command::SUCCESS;
    }
}