<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:create-user')]
class CreateUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Créer un admin
        $user = new User();
        $user->setEmail('jules.parents@gmail.com');
        $user->setNom('Parents');
        $user->setPrenom('Jules');
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'azerty');
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);

        $this->entityManager->persist($user);

        // Créer un utilisateur
        $user = new User();
        $user->setEmail('lenny.lecable@gmail.com');
        $user->setNom('Lecable');
        $user->setPrenom('Lenny');
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'qwerty');
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_USER']);

        $this->entityManager->persist($user);

        $this->entityManager->flush();

        $output->writeln('Utilisateurs (admin et user) créés avec succès !');
        return Command::SUCCESS;
    }
}