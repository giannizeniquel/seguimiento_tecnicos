<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Crea un usuario administrador por defecto'
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = 'admin@demo.com';
        
        $existingUser = $this->userRepository->findOneBy(['email' => $email]);
        if ($existingUser) {
            $io->warning('El usuario administrador ya existe');
            return Command::SUCCESS;
        }

        $user = new User();
        $user->setEmail($email);
        $user->setName('Administrador');
        $user->setRole('ADMIN');
        $user->setPhone('3001234567');
        $user->setPasswordHash($this->passwordHasher->hashPassword($user, 'admin123'));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('Usuario administrador creado exitosamente');
        $io->table(
            ['Email', 'Contraseña', 'Rol'],
            [
                [$email, 'admin123', 'ADMIN']
            ]
        );

        return Command::SUCCESS;
    }
}
