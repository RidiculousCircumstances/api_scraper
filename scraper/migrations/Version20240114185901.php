<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\DataSchema;
use App\Entity\RequestParameter;
use App\Migrations\AbstractRequestSeeder;
use App\Repository\DataSchema\DataSchemaRepository;
use App\Repository\GroupTagRepository;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240114185901 extends AbstractRequestSeeder
{
    public function getDescription(): string
    {
        return 'Сохранение пресета с тестовым запросом';
    }

    public function up(Schema $schema): void
    {
        $parameters = [
            'mainPhotoWidth' => 'original',
            'thumbnailsWidth[]' => [480, 'original'],
            'version' => 4,
            'recSysDeviceId' => '8d07d92c740264189e0bf99976f1aa4d',
            'recSysRegionId' => '22',
            'recSysCityId' => '11',
            'app_id' => 'p32',
            'timestamp' => '{{:timestamp}}',
            'secret' => '{{:secret}}',
            '{{:url_parameter=bulletinid}}' => 'data.cars.*.id',
        ];

        $url = 'https://api.drom.ru/v1.3/bulls/{{:url_parameter=bulletinid}}';


        /**
         * @var GroupTagRepository $groupTagRepo
         */
        $groupTagRepo = self::$container->get(GroupTagRepository::class);

        $groupTag = $groupTagRepo->findByCode('drom');

        $dataSchema = new DataSchema();
        $dataSchema
            ->setUrl($url)
            ->setName('detail page')
            ->setGroupTag($groupTag)
            ->setNeedsAuth(false);

        /**
         * @var DataSchemaRepository $dataSchemaRepo
         */
        $dataSchemaRepo = self::$container->get(DataSchemaRepository::class);

        $externalSchema = $dataSchemaRepo->findOneBy(['name' => 'drom car list']);

        $addExternalField = function (string $key, string $value, RequestParameter $requestParameter) use ($externalSchema) {
            if ($key === '{{:url_parameter=1}}') {
                $requestParameter->setExternalSchema($externalSchema);
                $externalSchema->addRequestParameter($requestParameter);
            }
        };

        $persistExternalSchema = function (EntityManagerInterface $entityManager) use ($externalSchema) {
            $entityManager->persist($externalSchema);
        };

        $this->createRequest($parameters, $dataSchema, $addExternalField, $persistExternalSchema);

    }

    public function down(Schema $schema): void
    {

    }
}
