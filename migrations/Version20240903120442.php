<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240903120442 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE issue_user DROP CONSTRAINT fk_d5741e855e7aa58c');
        $this->addSql('ALTER TABLE issue_user DROP CONSTRAINT fk_d5741e85a76ed395');
        $this->addSql('DROP TABLE issue_user');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE issue_user (issue_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(issue_id, user_id))');
        $this->addSql('CREATE INDEX idx_d5741e85a76ed395 ON issue_user (user_id)');
        $this->addSql('CREATE INDEX idx_d5741e855e7aa58c ON issue_user (issue_id)');
        $this->addSql('ALTER TABLE issue_user ADD CONSTRAINT fk_d5741e855e7aa58c FOREIGN KEY (issue_id) REFERENCES issue (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE issue_user ADD CONSTRAINT fk_d5741e85a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
