<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\Settings\Settings;
use App\Entity\Settings\SettingsTypeEnum;
use App\Migrations\AbstractRequestSeeder;
use App\Repository\SettingsRepository;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240115081643 extends AbstractRequestSeeder
{
    public function getDescription(): string
    {
        return 'Добавление настроек с прокси';
    }

    public function up(Schema $schema): void
    {
        $container = self::$container;

        $httpProxy = new Settings();

        $httpProxy
            ->setType(SettingsTypeEnum::HTTP_PROXIES->value)
            ->setValue('');

        $httpsProxy = new Settings();
        $httpsProxy
            ->setType(SettingsTypeEnum::HTTPS_PROXIES->value)
            ->setValue('');
        $em = $this->getEntityManager();
        $em->persist($httpProxy);
        $em->persist($httpsProxy);

        $em->flush();
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
    }
}
