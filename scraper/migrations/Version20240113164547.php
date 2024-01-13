<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240113164547 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'not null для внешних ключей связанных с GroupTag сущностей';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE data_schema ALTER group_tag_id SET NOT NULL');
        $this->addSql('ALTER TABLE output_schema ALTER group_tag_id SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE output_schema ALTER group_tag_id DROP NOT NULL');
        $this->addSql('ALTER TABLE data_schema ALTER group_tag_id DROP NOT NULL');
    }
}
