<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220712154539 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE repair_asset');
        $this->addSql('DROP TABLE repair_person');
        $this->addSql('DROP TABLE repair_slot');
        $this->addSql('DROP TABLE repair_user');
        $this->addSql('ALTER TABLE repair ADD assetid INT NOT NULL, ADD personid INT NOT NULL, ADD technicianid INT NOT NULL, ADD cart_slot_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE repair_asset (repair_id INT NOT NULL, asset_id INT NOT NULL, INDEX IDX_A2709EF043833CFF (repair_id), INDEX IDX_A2709EF05DA1941 (asset_id), PRIMARY KEY(repair_id, asset_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE repair_person (repair_id INT NOT NULL, person_id INT NOT NULL, INDEX IDX_EB1CE171217BBB47 (person_id), INDEX IDX_EB1CE17143833CFF (repair_id), PRIMARY KEY(repair_id, person_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE repair_slot (repair_id INT NOT NULL, slot_id INT NOT NULL, INDEX IDX_7928F48743833CFF (repair_id), INDEX IDX_7928F48759E5119C (slot_id), PRIMARY KEY(repair_id, slot_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE repair_user (repair_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_58B502A943833CFF (repair_id), INDEX IDX_58B502A9A76ED395 (user_id), PRIMARY KEY(repair_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE repair_asset ADD CONSTRAINT FK_A2709EF043833CFF FOREIGN KEY (repair_id) REFERENCES repair (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE repair_asset ADD CONSTRAINT FK_A2709EF05DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE repair_person ADD CONSTRAINT FK_EB1CE17143833CFF FOREIGN KEY (repair_id) REFERENCES repair (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE repair_person ADD CONSTRAINT FK_EB1CE171217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE repair_slot ADD CONSTRAINT FK_7928F48743833CFF FOREIGN KEY (repair_id) REFERENCES repair (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE repair_slot ADD CONSTRAINT FK_7928F48759E5119C FOREIGN KEY (slot_id) REFERENCES slot (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE repair_user ADD CONSTRAINT FK_58B502A943833CFF FOREIGN KEY (repair_id) REFERENCES repair (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE repair_user ADD CONSTRAINT FK_58B502A9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE repair DROP assetid, DROP personid, DROP technicianid, DROP cart_slot_id');
    }
}
