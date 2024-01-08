<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migrations: Please modify to your needs!
 */
final class Version20240108151144 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE data_schema_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE group_tag_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE output_schema_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE request_parameter_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE response_field_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE data_schema (id INT NOT NULL, group_tag_id INT DEFAULT NULL, name VARCHAR(20) NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D870704B6954BBC1 ON data_schema (group_tag_id)');
        $this->addSql('CREATE TABLE group_tag (id INT NOT NULL, code VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX group_code ON group_tag (code)');
        $this->addSql('CREATE TABLE output_schema (id INT NOT NULL, group_tag_id INT DEFAULT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6C537BBB6954BBC1 ON output_schema (group_tag_id)');
        $this->addSql('CREATE TABLE output_schema_response_field (output_schema_id INT NOT NULL, response_field_id INT NOT NULL, PRIMARY KEY(output_schema_id, response_field_id))');
        $this->addSql('CREATE INDEX IDX_F761C68D91E374DE ON output_schema_response_field (output_schema_id)');
        $this->addSql('CREATE INDEX IDX_F761C68DF321E1CD ON output_schema_response_field (response_field_id)');
        $this->addSql('CREATE TABLE request_parameter (id INT NOT NULL, data_schema_id INT NOT NULL, external_schema_id INT DEFAULT NULL, key VARCHAR(50) NOT NULL, value VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_616660B765B4AF03 ON request_parameter (data_schema_id)');
        $this->addSql('CREATE INDEX IDX_616660B7CE404021 ON request_parameter (external_schema_id)');
        $this->addSql('CREATE TABLE response_field (id INT NOT NULL, data_schema_id INT NOT NULL, data_path VARCHAR(255) NOT NULL, output_name VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4B839E0865B4AF03 ON response_field (data_schema_id)');
        $this->addSql('ALTER TABLE data_schema ADD CONSTRAINT FK_D870704B6954BBC1 FOREIGN KEY (group_tag_id) REFERENCES group_tag (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE output_schema ADD CONSTRAINT FK_6C537BBB6954BBC1 FOREIGN KEY (group_tag_id) REFERENCES group_tag (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE output_schema_response_field ADD CONSTRAINT FK_F761C68D91E374DE FOREIGN KEY (output_schema_id) REFERENCES output_schema (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE output_schema_response_field ADD CONSTRAINT FK_F761C68DF321E1CD FOREIGN KEY (response_field_id) REFERENCES response_field (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE request_parameter ADD CONSTRAINT FK_616660B765B4AF03 FOREIGN KEY (data_schema_id) REFERENCES data_schema (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE request_parameter ADD CONSTRAINT FK_616660B7CE404021 FOREIGN KEY (external_schema_id) REFERENCES data_schema (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE response_field ADD CONSTRAINT FK_4B839E0865B4AF03 FOREIGN KEY (data_schema_id) REFERENCES data_schema (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE data_schema_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE group_tag_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE output_schema_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE request_parameter_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE response_field_id_seq CASCADE');
        $this->addSql('ALTER TABLE data_schema DROP CONSTRAINT FK_D870704B6954BBC1');
        $this->addSql('ALTER TABLE output_schema DROP CONSTRAINT FK_6C537BBB6954BBC1');
        $this->addSql('ALTER TABLE output_schema_response_field DROP CONSTRAINT FK_F761C68D91E374DE');
        $this->addSql('ALTER TABLE output_schema_response_field DROP CONSTRAINT FK_F761C68DF321E1CD');
        $this->addSql('ALTER TABLE request_parameter DROP CONSTRAINT FK_616660B765B4AF03');
        $this->addSql('ALTER TABLE request_parameter DROP CONSTRAINT FK_616660B7CE404021');
        $this->addSql('ALTER TABLE response_field DROP CONSTRAINT FK_4B839E0865B4AF03');
        $this->addSql('DROP TABLE data_schema');
        $this->addSql('DROP TABLE group_tag');
        $this->addSql('DROP TABLE output_schema');
        $this->addSql('DROP TABLE output_schema_response_field');
        $this->addSql('DROP TABLE request_parameter');
        $this->addSql('DROP TABLE response_field');
    }
}
