<?php

namespace App\Command;

use App\Entity\Organization;
use App\Entity\OrganizationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FillDictionariesCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager, string $name = 'dictionaries:fill')
    {
        $this->entityManager = $entityManager;
        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->section('Заполнение типов организаций');
        $organizationsTypes = $this->fillOrganizationsGroups();
        $io->success('Успех');
        $io->section('Заполнение организаций');
        $this->fillOrganizations($organizationsTypes);
        $io->success('Успех');
    }

    private function fillOrganizationsGroups(): array
    {
        $organizationsTypes = require __DIR__ . '/OrganizationsTypes.php';

        foreach ($organizationsTypes as $key => $organizationType) {
            if (null === $organizationType) continue;

            $org = new OrganizationType();
            $org->setName($organizationType);
            $this->entityManager->persist($org);
            $this->entityManager->flush();

            $organizationsTypes[$key] = $org;
        }

        return $organizationsTypes;
    }

    private function fillOrganizations(array $organizationsTypes): void
    {
        $organizations = require __DIR__ . '/Organizations.php';

        foreach ($organizations as $organization) {
            $org = new Organization();

            $org->setName($organization['name'])
                ->setType($organizationsTypes[$organization['orgType']]);

            if (isset($organization['branches'])) {
                $org->setBranches($organization['branches']);
            }

            $this->entityManager->persist($org);
        }

        $this->entityManager->flush();
    }
}