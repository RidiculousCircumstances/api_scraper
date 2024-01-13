<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\DataSchema;
use App\Entity\RequestParameter;
use App\Migrations\Factory\ContainerAwareInterface;
use App\Repository\DataSchema\DataSchemaRepository;
use App\Repository\GroupTagRepository;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\NonUniqueResultException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240112142711 extends AbstractMigration implements ContainerAwareInterface
{

    private ContainerInterface $container;

    public function getDescription(): string
    {
        return 'Сохранение пресета с тестовым запросом';
    }

    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NonUniqueResultException
     * @throws NotFoundExceptionInterface
     */
    public function up(Schema $schema): void
    {
        $parameters = [
            'homeRegionId' => 22,
            'recSysDeviceId' => '8d07d92c740264189e0bf99976f1aa4d',
            'recSysRegionId' => 22,
            'recSysCityId' => 11,
            'app_id' => 'p32',
            'timestamp' => '{{:timestamp}}',
            'secret' => '{{:secret}}',
            '{{:url_parameter=bulletinid}}' => 'data.cars.*.id',
        ];

        $url = 'https://api.drom.ru/v1.3/bulls/{{:url_parameter=bulletinid}}/contacts';

        /**
         * @var GroupTagRepository $groupTagRepo
         */
        $groupTagRepo = $this->container->get(GroupTagRepository::class);

        $groupTag = $groupTagRepo->findByCode('drom');

        $dataSchema = new DataSchema();
        $dataSchema
            ->setUrl($url)
            ->setName('contacts')
            ->setGroupTag($groupTag)
            ->setNeedsAuth(true);

        /**
         * @var DataSchemaRepository $dataSchemaRepo
         */
        $dataSchemaRepo = $this->container->get(DataSchemaRepository::class);


        $externalSchema = $dataSchemaRepo->findBy(['name' => 'drom car list']);

        /**
         * @var DataSchema $externalSchema
         */
        $externalSchema = $externalSchema[0];

        foreach ($parameters as $key => $value) {
            $requestParameter = new RequestParameter();

            if ($key === '{{:url_parameter=bulletinid}}') {
                $externalSchema->addRequestParameter($requestParameter);
                $requestParameter->setExternalSchema($externalSchema);
            }

            $requestParameter
                ->setKey($key)
                ->setValue((string)$value);

            $dataSchema->addRequestParameter($requestParameter);

        }

        $em = $this->container->get('doctrine.orm.default_entity_manager');

        $em->persist($dataSchema);
        $em->persist($externalSchema);
        $em->flush();

    }

    public function down(Schema $schema): void
    {

    }
}
