<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\DataSchema;
use App\Migrations\AbstractRequestSeeder;
use App\Repository\DataSchema\DataSchemaRepository;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240114185904 extends AbstractRequestSeeder
{
    public function getDescription(): string
    {
        return 'Добавление порядка исполнения дата схемам';
    }

    public function up(Schema $schema): void
    {
        $container = self::$container;

        /**
         * @var DataSchemaRepository $dsRepo
         */
        $dsRepo = $container->get(DataSchemaRepository::class);

        /**
         * @var DataSchema $carList
         */
        $carList = $dsRepo->findOneBy(['name' => 'drom car list']);
        $carList->setExecutionOrder(1)->setName('drom_car_list');

        $detail = $dsRepo->findOneBy(['name' => 'detail page']);
        $detail->setExecutionOrder(2)->setName('detail_page');

        $contacts = $dsRepo->findOneBy(['name' => 'contacts']);
        $contacts->setMute(true);

        $em = $this->getEntityManager();
        $em->persist($carList);
        $em->persist($detail);
        $em->persist($contacts);
        $em->flush();
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
    }
}
