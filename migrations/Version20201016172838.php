<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201016172838 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE journal ALTER COLUMN note VARCHAR(MAX)');
        $this->addSql('ALTER TABLE organization ALTER COLUMN contact VARCHAR(MAX)');
        $this->addSql('ALTER TABLE organization ALTER COLUMN is_active BIT');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA db_accessadmin');
        $this->addSql('CREATE SCHEMA db_backupoperator');
        $this->addSql('CREATE SCHEMA db_datareader');
        $this->addSql('CREATE SCHEMA db_datawriter');
        $this->addSql('CREATE SCHEMA db_ddladmin');
        $this->addSql('CREATE SCHEMA db_denydatareader');
        $this->addSql('CREATE SCHEMA db_denydatawriter');
        $this->addSql('CREATE SCHEMA db_owner');
        $this->addSql('CREATE SCHEMA db_securityadmin');
        $this->addSql('CREATE SCHEMA dbo');
        $this->addSql('ALTER TABLE journal ALTER COLUMN note VARCHAR(MAX) COLLATE Cyrillic_General_CI_AS');
        $this->addSql('ALTER TABLE organization ALTER COLUMN contact VARCHAR(MAX) COLLATE Cyrillic_General_CI_AS');
        $this->addSql('ALTER TABLE organization ALTER COLUMN is_active BIT');
        $this->addSql('ALTER TABLE organization ADD CONSTRAINT DF_C1EE637C_1B5771DD DEFAULT \'1\' FOR is_active');
    }
}
