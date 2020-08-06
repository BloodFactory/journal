<?php

namespace App\Command;

use App\Entity\Organization;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateUserCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, string $name = 'user:create')
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;

        parent::__construct($name);
    }

    public function configure()
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'Логин пользователя')
            ->addArgument('password', InputArgument::REQUIRED, 'Пароль')
            ->addArgument('organization', InputArgument::REQUIRED, 'Код организации. Для просмотра всех организаций введит команду list:organizations')
            ->addArgument('roles', InputArgument::IS_ARRAY, 'Роли', []);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $organizationCode = (int)$input->getArgument('organization');
        $roles = (array)$input->getArgument('roles');

        $organization = $this->entityManager->getRepository(Organization::class)->find($organizationCode);

        if (!$organization instanceof Organization) {
            $io->error("организации с кодом {$organizationCode} не существует");
            return 1;
        }

        $user = new User();
        $user
            ->setUsername($username)
            ->setPassword($this->passwordEncoder->encodePassword($user, $password))
            ->setOrganization($organization)
            ->setRoles($roles);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}