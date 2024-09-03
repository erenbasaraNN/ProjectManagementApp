<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240903085709 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE brand_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE issue_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE project_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE task_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE brand (id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE issue (id INT NOT NULL, task_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, assigned_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_12AD233E8DB60186 ON issue (task_id)');
        $this->addSql('CREATE TABLE issue_user (issue_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(issue_id, user_id))');
        $this->addSql('CREATE INDEX IDX_D5741E855E7AA58C ON issue_user (issue_id)');
        $this->addSql('CREATE INDEX IDX_D5741E85A76ED395 ON issue_user (user_id)');
        $this->addSql('CREATE TABLE project (id INT NOT NULL, brand_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE44F5D008 ON project (brand_id)');
        $this->addSql('CREATE TABLE task (id INT NOT NULL, project_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_527EDB25166D1F9C ON task (project_id)');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(180) DEFAULT NULL, roles JSON DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233E8DB60186 FOREIGN KEY (task_id) REFERENCES task (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE issue_user ADD CONSTRAINT FK_D5741E855E7AA58C FOREIGN KEY (issue_id) REFERENCES issue (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE issue_user ADD CONSTRAINT FK_D5741E85A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE44F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE brand_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE issue_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE project_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE task_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_id_seq CASCADE');
        $this->addSql('ALTER TABLE issue DROP CONSTRAINT FK_12AD233E8DB60186');
        $this->addSql('ALTER TABLE issue_user DROP CONSTRAINT FK_D5741E855E7AA58C');
        $this->addSql('ALTER TABLE issue_user DROP CONSTRAINT FK_D5741E85A76ED395');
        $this->addSql('ALTER TABLE project DROP CONSTRAINT FK_2FB3D0EE44F5D008');
        $this->addSql('ALTER TABLE task DROP CONSTRAINT FK_527EDB25166D1F9C');
        $this->addSql('DROP TABLE brand');
        $this->addSql('DROP TABLE issue');
        $this->addSql('DROP TABLE issue_user');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE "user"');
    }
}
