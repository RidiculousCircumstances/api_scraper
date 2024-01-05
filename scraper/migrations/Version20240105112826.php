<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240105112826 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE output_schema_response_field (output_schema_id INT NOT NULL, response_field_id INT NOT NULL, PRIMARY KEY(output_schema_id, response_field_id))');
        $this->addSql('CREATE INDEX IDX_F761C68D91E374DE ON output_schema_response_field (output_schema_id)');
        $this->addSql('CREATE INDEX IDX_F761C68DF321E1CD ON output_schema_response_field (response_field_id)');
        $this->addSql('ALTER TABLE output_schema_response_field ADD CONSTRAINT FK_F761C68D91E374DE FOREIGN KEY (output_schema_id) REFERENCES output_schema (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE output_schema_response_field ADD CONSTRAINT FK_F761C68DF321E1CD FOREIGN KEY (response_field_id) REFERENCES response_field (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE output_schema DROP CONSTRAINT fk_6c537bbbb7b1bb8');
        $this->addSql('DROP INDEX idx_6c537bbbb7b1bb8');
        $this->addSql('ALTER TABLE output_schema ADD group_tag_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE output_schema DROP group_tags_id');
        $this->addSql('ALTER TABLE output_schema ADD CONSTRAINT FK_6C537BBB6954BBC1 FOREIGN KEY (group_tag_id) REFERENCES group_tag (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_6C537BBB6954BBC1 ON output_schema (group_tag_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE output_schema_response_field DROP CONSTRAINT FK_F761C68D91E374DE');
        $this->addSql('ALTER TABLE output_schema_response_field DROP CONSTRAINT FK_F761C68DF321E1CD');
        $this->addSql('DROP TABLE output_schema_response_field');
        $this->addSql('ALTER TABLE output_schema DROP CONSTRAINT FK_6C537BBB6954BBC1');
        $this->addSql('DROP INDEX IDX_6C537BBB6954BBC1');
        $this->addSql('ALTER TABLE output_schema ADD group_tags_id INT NOT NULL');
        $this->addSql('ALTER TABLE output_schema DROP group_tag_id');
        $this->addSql('ALTER TABLE output_schema ADD CONSTRAINT fk_6c537bbbb7b1bb8 FOREIGN KEY (group_tags_id) REFERENCES group_tag (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_6c537bbbb7b1bb8 ON output_schema (group_tags_id)');
    }
}
