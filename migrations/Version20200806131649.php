<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200806131649 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE organization ADD type_id INT NOT NULL');
        $this->addSql('ALTER TABLE organization ADD CONSTRAINT FK_C1EE637CC54C8C93 FOREIGN KEY (type_id) REFERENCES organization_type (id)');
        $this->addSql('CREATE INDEX IDX_C1EE637CC54C8C93 ON organization (type_id)');
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
        $this->addSql('ALTER TABLE organization DROP CONSTRAINT FK_C1EE637CC54C8C93');
        $this->addSql('IF EXISTS (SELECT * FROM sysobjects WHERE name = \'IDX_C1EE637CC54C8C93\')
            ALTER TABLE organization DROP CONSTRAINT IDX_C1EE637CC54C8C93
        ELSE
            DROP INDEX IDX_C1EE637CC54C8C93 ON organization');
        $this->addSql('ALTER TABLE organization DROP COLUMN type_id');
    }
}
