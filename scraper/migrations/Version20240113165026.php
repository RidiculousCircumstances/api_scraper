<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Migrations\AbstractRequestSeeder;
use App\Repository\DataSchema\DataSchemaRepository;
use Doctrine\DBAL\Schema\Schema;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240113165026 extends AbstractRequestSeeder
{
    public function getDescription(): string
    {
        return 'Пресет схемы парсинга';
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function up(Schema $schema): void
    {
        $container = self::$container;

        /**
         * @var DataSchemaRepository $dataSchemaRepo
         */
        $dataSchemaRepo = $container->get(DataSchemaRepository::class);

        $carListDataSchema = $dataSchemaRepo->findOneBy(['name' => 'drom car list']);

        $responseCarListFields = [
            'data.cars.*.id' => 'id объявления',
            'data.cars.*.url' => 'url объявления',
            'data.cars.*.pretty.firm' => 'Марка авто',
            'data.cars.*.model' => 'Модель авто',
            'data.cars.*.price.rub' => 'Цена продажи',
            'data.cars.*.priceCategory' => 'Категория цены',
            'data.cars.*.complectation.name' => 'Комплектация авто',
            'data.cars.*.mileageKm' => 'Пробег',
            'data.cars.*.notUsedInRussia' => 'Без пробега по РФ',
            'data.cars.*.pretty.color' => 'Цвет',
            'data.cars.*.pretty.frameType' => 'Тип кузова',
            'data.cars.*.enginePower' => 'Мощность двигателя',
            'data.cars.*.fuelType' => 'Тип топлива',
            'data.cars.*.engineVolume' => 'Объём двигателя'
        ];

        $this->createResponse($responseCarListFields, $carListDataSchema);

        $detailDataSchema = $dataSchemaRepo->findOneBy(['name' => 'detail page']);

        $responseDetail = [
            'payload.year' => 'Год выпуска',
            'payload.mainPhoto' => 'Основное фото',
            'payload.viewsTotal' => 'Просмотры',
            'payload.shortDescription' => 'Короткое описание'
        ];

        $this->createResponse($responseDetail, $detailDataSchema);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
    }
}
