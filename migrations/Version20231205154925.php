<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231205154925 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE postes ADD user_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE postes ADD CONSTRAINT FK_5A763C649D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_5A763C649D86650F ON postes (user_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE postes DROP FOREIGN KEY FK_5A763C649D86650F');
        $this->addSql('DROP INDEX IDX_5A763C649D86650F ON postes');
        $this->addSql('ALTER TABLE postes DROP user_id_id');
    }
}
