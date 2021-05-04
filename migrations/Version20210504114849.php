<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210504114849 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE [request_log] ([id] INT IDENTITY NOT NULL, [usr_id] INT, [moment] DATETIME2(6) NOT NULL, [content] VARCHAR(MAX), [method] NVARCHAR(15) NOT NULL, [path] NVARCHAR(255) NOT NULL, [query] VARCHAR(MAX), [request] VARCHAR(MAX), PRIMARY KEY ([id]))');
        $this->addSql('CREATE INDEX [IDX_42152989C69D3FB] ON [request_log] ([usr_id])');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:array)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'request_log\', N\'COLUMN\', content');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:array)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'request_log\', N\'COLUMN\', query');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:array)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'request_log\', N\'COLUMN\', request');
        $this->addSql('ALTER TABLE [request_log] ADD CONSTRAINT [FK_42152989C69D3FB] FOREIGN KEY ([usr_id]) REFERENCES [usr] ([id])');
        $this->addSql('ALTER TABLE [journal] ALTER COLUMN [note] VARCHAR(MAX)');
        $this->addSql('ALTER TABLE [organization] ALTER COLUMN [contact] VARCHAR(MAX)');
        $this->addSql('ALTER TABLE [organization] ALTER COLUMN [is_active] BIT');
    }

    public function down(Schema $schema): void
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
        $this->addSql('DROP TABLE request_log');
        $this->addSql('ALTER TABLE [journal] ALTER COLUMN [note] VARCHAR(MAX) COLLATE [Cyrillic_General_CI_AS]');
        $this->addSql('ALTER TABLE organization ALTER COLUMN contact VARCHAR(MAX) COLLATE Cyrillic_General_CI_AS');
        $this->addSql('ALTER TABLE organization ALTER COLUMN is_active BIT');
        $this->addSql('ALTER TABLE organization ADD CONSTRAINT DF_C1EE637C_1B5771DD DEFAULT \'1\' FOR is_active');
    }
}
