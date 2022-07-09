<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220706105128 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE import (id INT AUTO_INCREMENT NOT NULL, imported_by_id INT DEFAULT NULL, imported_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', imported_data LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_9D4ECE1D74953CEA (imported_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE import ADD CONSTRAINT FK_9D4ECE1D74953CEA FOREIGN KEY (imported_by_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE import');
    }
}
