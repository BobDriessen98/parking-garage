<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260727132209 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE parking_session ADD garage_id INT NOT NULL');
        $this->addSql('ALTER TABLE parking_session ADD CONSTRAINT FK_5EAE2D0EC4FFF555 FOREIGN KEY (garage_id) REFERENCES parking_garage (id) NOT DEFERRABLE');
        $this->addSql('CREATE INDEX IDX_5EAE2D0EC4FFF555 ON parking_session (garage_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE parking_session DROP CONSTRAINT FK_5EAE2D0EC4FFF555');
        $this->addSql('DROP INDEX IDX_5EAE2D0EC4FFF555');
        $this->addSql('ALTER TABLE parking_session DROP garage_id');
    }
}
