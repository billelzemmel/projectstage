<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231205153118 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE postes (id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, text VARCHAR(255) DEFAULT NULL, photo LONGBLOB DEFAULT NULL, nb_react INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE postes_user_user (postes_id INT NOT NULL, user_user_id INT NOT NULL, INDEX IDX_471D82BFE30A0B60 (postes_id), INDEX IDX_471D82BFFF63CD9F (user_user_id), PRIMARY KEY(postes_id, user_user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE postes_user_user ADD CONSTRAINT FK_471D82BFE30A0B60 FOREIGN KEY (postes_id) REFERENCES postes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE postes_user_user ADD CONSTRAINT FK_471D82BFFF63CD9F FOREIGN KEY (user_user_id) REFERENCES user_user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE postes_user_user DROP FOREIGN KEY FK_471D82BFE30A0B60');
        $this->addSql('ALTER TABLE postes_user_user DROP FOREIGN KEY FK_471D82BFFF63CD9F');
        $this->addSql('DROP TABLE postes');
        $this->addSql('DROP TABLE postes_user_user');
    }
}
