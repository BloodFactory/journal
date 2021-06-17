<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210617122123 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE [alert] ([id] INT IDENTITY NOT NULL, [message] VARCHAR(MAX) NOT NULL, [begin_at] DATETIME2(6), [end_at] DATETIME2(6), [once] BIT NOT NULL, PRIMARY KEY ([id]))');
        $this->addSql('CREATE TABLE [user_alert] ([id] INT IDENTITY NOT NULL, [usr_id] INT NOT NULL, [alert_id] INT NOT NULL, [date] DATETIME2(6) NOT NULL, PRIMARY KEY ([id]))');
        $this->addSql('CREATE INDEX [IDX_F53FBD99C69D3FB] ON [user_alert] ([usr_id])');
        $this->addSql('CREATE INDEX [IDX_F53FBD9993035F72] ON [user_alert] ([alert_id])');
        $this->addSql('ALTER TABLE [user_alert] ADD CONSTRAINT [FK_F53FBD99C69D3FB] FOREIGN KEY ([usr_id]) REFERENCES [usr] ([id])');
        $this->addSql('ALTER TABLE [user_alert] ADD CONSTRAINT [FK_F53FBD9993035F72] FOREIGN KEY ([alert_id]) REFERENCES [alert] ([id])');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE [user_alert] DROP CONSTRAINT [FK_F53FBD9993035F72]');
        $this->addSql('DROP TABLE [alert]');
        $this->addSql('DROP TABLE [user_alert]');
        $this->addSql('ALTER TABLE [journal] ALTER COLUMN [note] VARCHAR(MAX) COLLATE [Cyrillic_General_CI_AS]');
        $this->addSql('ALTER TABLE [organization] ALTER COLUMN [contact] VARCHAR(MAX) COLLATE [Cyrillic_General_CI_AS]');
        $this->addSql('ALTER TABLE [organization] ALTER COLUMN [is_active] BIT');
        $this->addSql('ALTER TABLE [organization] ADD CONSTRAINT [DF_C1EE637C_1B5771DD] DEFAULT \'1\' FOR [is_active]');
    }
}
