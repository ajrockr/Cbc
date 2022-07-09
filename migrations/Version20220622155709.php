<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220622155709 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE slot ADD assigned_person_id_id INT DEFAULT NULL, ADD assigned_asset_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE slot ADD CONSTRAINT FK_AC0E206731EDC167 FOREIGN KEY (assigned_person_id_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE slot ADD CONSTRAINT FK_AC0E2067CD2550D9 FOREIGN KEY (assigned_asset_id_id) REFERENCES asset (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AC0E206731EDC167 ON slot (assigned_person_id_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AC0E2067CD2550D9 ON slot (assigned_asset_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE slot DROP FOREIGN KEY FK_AC0E206731EDC167');
        $this->addSql('ALTER TABLE slot DROP FOREIGN KEY FK_AC0E2067CD2550D9');
        $this->addSql('DROP INDEX UNIQ_AC0E206731EDC167 ON slot');
        $this->addSql('DROP INDEX UNIQ_AC0E2067CD2550D9 ON slot');
        $this->addSql('ALTER TABLE slot DROP assigned_person_id_id, DROP assigned_asset_id_id');
    }
}
