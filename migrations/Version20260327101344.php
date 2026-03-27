<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260327101344 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE infos ADD user_id INT NOT NULL, DROP user');
        $this->addSql('ALTER TABLE infos ADD CONSTRAINT FK_EECA826DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EECA826DA76ED395 ON infos (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE infos DROP FOREIGN KEY FK_EECA826DA76ED395');
        $this->addSql('DROP INDEX UNIQ_EECA826DA76ED395 ON infos');
        $this->addSql('ALTER TABLE infos ADD user VARCHAR(255) NOT NULL, DROP user_id');
    }
}
