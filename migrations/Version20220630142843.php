<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220630142843 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE repair (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', modified_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', active TINYINT(1) NOT NULL, closed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', notes VARCHAR(255) DEFAULT NULL, items LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE repair_asset (repair_id INT NOT NULL, asset_id INT NOT NULL, INDEX IDX_A2709EF043833CFF (repair_id), INDEX IDX_A2709EF05DA1941 (asset_id), PRIMARY KEY(repair_id, asset_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE repair_person (repair_id INT NOT NULL, person_id INT NOT NULL, INDEX IDX_EB1CE17143833CFF (repair_id), INDEX IDX_EB1CE171217BBB47 (person_id), PRIMARY KEY(repair_id, person_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE repair_user (repair_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_58B502A943833CFF (repair_id), INDEX IDX_58B502A9A76ED395 (user_id), PRIMARY KEY(repair_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE repair_slot (repair_id INT NOT NULL, slot_id INT NOT NULL, INDEX IDX_7928F48743833CFF (repair_id), INDEX IDX_7928F48759E5119C (slot_id), PRIMARY KEY(repair_id, slot_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE repair_item (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, cost DOUBLE PRECISION DEFAULT NULL, supplier VARCHAR(255) DEFAULT NULL, supplier_url VARCHAR(255) DEFAULT NULL, supplier_email VARCHAR(255) DEFAULT NULL, supplier_phone VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE repair_asset ADD CONSTRAINT FK_A2709EF043833CFF FOREIGN KEY (repair_id) REFERENCES repair (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE repair_asset ADD CONSTRAINT FK_A2709EF05DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE repair_person ADD CONSTRAINT FK_EB1CE17143833CFF FOREIGN KEY (repair_id) REFERENCES repair (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE repair_person ADD CONSTRAINT FK_EB1CE171217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE repair_user ADD CONSTRAINT FK_58B502A943833CFF FOREIGN KEY (repair_id) REFERENCES repair (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE repair_user ADD CONSTRAINT FK_58B502A9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE repair_slot ADD CONSTRAINT FK_7928F48743833CFF FOREIGN KEY (repair_id) REFERENCES repair (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE repair_slot ADD CONSTRAINT FK_7928F48759E5119C FOREIGN KEY (slot_id) REFERENCES slot (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE repair_asset DROP FOREIGN KEY FK_A2709EF043833CFF');
        $this->addSql('ALTER TABLE repair_person DROP FOREIGN KEY FK_EB1CE17143833CFF');
        $this->addSql('ALTER TABLE repair_user DROP FOREIGN KEY FK_58B502A943833CFF');
        $this->addSql('ALTER TABLE repair_slot DROP FOREIGN KEY FK_7928F48743833CFF');
        $this->addSql('DROP TABLE repair');
        $this->addSql('DROP TABLE repair_asset');
        $this->addSql('DROP TABLE repair_person');
        $this->addSql('DROP TABLE repair_user');
        $this->addSql('DROP TABLE repair_slot');
        $this->addSql('DROP TABLE repair_item');
    }
}
