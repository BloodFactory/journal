<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200806084507 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE journal (id INT IDENTITY NOT NULL, organization_id INT NOT NULL, total INT NOT NULL, at_work INT NOT NULL, on_holiday INT NOT NULL, remote_total INT NOT NULL, remote_pregnant INT NOT NULL, remote_with_children INT NOT NULL, remote_over60 INT NOT NULL, on_two_week_quarantine INT NOT NULL, on_sick_leave INT NOT NULL, sick_covid INT NOT NULL, note NVARCHAR(4000) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_C1A7E74D32C8A3DE ON journal (organization_id)');
        $this->addSql('CREATE TABLE organization (id INT IDENTITY NOT NULL, name NVARCHAR(255) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('ALTER TABLE journal ADD CONSTRAINT FK_C1A7E74D32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
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
        $this->addSql('ALTER TABLE journal DROP CONSTRAINT FK_C1A7E74D32C8A3DE');
        $this->addSql('DROP TABLE journal');
        $this->addSql('DROP TABLE organization');
    }
}
