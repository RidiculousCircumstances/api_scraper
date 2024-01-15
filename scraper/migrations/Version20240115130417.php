<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Migrations\AbstractRequestSeeder;
use App\Repository\ResponseField\ResponseFieldRepository;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240115130417 extends AbstractRequestSeeder
{
    public function getDescription(): string
    {
        return 'Модификация схемы для загрузки файлов';
    }

    public function up(Schema $schema): void
    {
        /**
         * @var ResponseFieldRepository $requestFieldRepo
         */
        $requestFieldRepo = self::$container->get(ResponseFieldRepository::class);
        $imageableField = $requestFieldRepo->findOneBy(['dataPath' => 'payload.mainPhoto.0.url']);

        $imageableField->setOutputName('{{:file=id объявления}}');

        $em = $this->getEntityManager();

        $em->persist($imageableField);
        $em->flush();

    }

    public function down(Schema $schema): void
    {

    }
}
