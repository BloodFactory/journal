<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200820082934 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE journal ADD die INT');
        $this->addSql('ALTER TABLE organization ALTER COLUMN contact VARCHAR(MAX)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE journal DROP COLUMN die');
        $this->addSql('ALTER TABLE organization ALTER COLUMN contact VARCHAR(MAX) COLLATE Cyrillic_General_CI_AS');
    }
}
