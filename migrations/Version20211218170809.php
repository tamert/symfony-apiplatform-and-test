<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211218170809 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE plan DROP CONSTRAINT fk_dd5a5b7da2e34e2a');
        $this->addSql('DROP INDEX idx_dd5a5b7da2e34e2a');
        $this->addSql('ALTER TABLE plan RENAME COLUMN resolved_ny_id TO resolved_by_id');
        $this->addSql('ALTER TABLE plan ADD CONSTRAINT FK_DD5A5B7D6713A32B FOREIGN KEY (resolved_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_DD5A5B7D6713A32B ON plan (resolved_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE plan DROP CONSTRAINT FK_DD5A5B7D6713A32B');
        $this->addSql('DROP INDEX IDX_DD5A5B7D6713A32B');
        $this->addSql('ALTER TABLE plan RENAME COLUMN resolved_by_id TO resolved_ny_id');
        $this->addSql('ALTER TABLE plan ADD CONSTRAINT fk_dd5a5b7da2e34e2a FOREIGN KEY (resolved_ny_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_dd5a5b7da2e34e2a ON plan (resolved_ny_id)');
    }
}
