<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231204142820 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_user (id INT AUTO_INCREMENT NOT NULL, user_source_id INT DEFAULT NULL, user_target_id INT DEFAULT NULL, etat VARCHAR(255) DEFAULT NULL, INDEX IDX_F7129A8095DC9185 (user_source_id), INDEX IDX_F7129A80156E8682 (user_target_id), UNIQUE INDEX unique_user_relation (user_source_id, user_target_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A8095DC9185 FOREIGN KEY (user_source_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A80156E8682 FOREIGN KEY (user_target_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY FK_F7129A8095DC9185');
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY FK_F7129A80156E8682');
        $this->addSql('DROP TABLE user_user');
    }
}
