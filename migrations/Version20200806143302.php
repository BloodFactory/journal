<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200806143302 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE usr ADD organization_id INT NOT NULL');
        $this->addSql('ALTER TABLE usr ADD CONSTRAINT FK_1762498C32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('CREATE INDEX IDX_1762498C32C8A3DE ON usr (organization_id)');
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
        $this->addSql('ALTER TABLE usr DROP CONSTRAINT FK_1762498C32C8A3DE');
        $this->addSql('IF EXISTS (SELECT * FROM sysobjects WHERE name = \'IDX_1762498C32C8A3DE\')
            ALTER TABLE usr DROP CONSTRAINT IDX_1762498C32C8A3DE
        ELSE
            DROP INDEX IDX_1762498C32C8A3DE ON usr');
        $this->addSql('ALTER TABLE usr DROP COLUMN organization_id');
    }
}
