<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200807070537 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE journal ADD head_office_id INT');
        $this->addSql('ALTER TABLE journal ADD CONSTRAINT FK_C1A7E74D7FAF4F07 FOREIGN KEY (head_office_id) REFERENCES journal (id)');
        $this->addSql('CREATE INDEX IDX_C1A7E74D7FAF4F07 ON journal (head_office_id)');
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
        $this->addSql('ALTER TABLE journal DROP CONSTRAINT FK_C1A7E74D7FAF4F07');
        $this->addSql('IF EXISTS (SELECT * FROM sysobjects WHERE name = \'IDX_C1A7E74D7FAF4F07\')
            ALTER TABLE journal DROP CONSTRAINT IDX_C1A7E74D7FAF4F07
        ELSE
            DROP INDEX IDX_C1A7E74D7FAF4F07 ON journal');
        $this->addSql('ALTER TABLE journal DROP COLUMN head_office_id');
    }
}
