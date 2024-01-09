<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\DataSchema;
use App\Entity\GroupTag;
use App\Entity\RequestParameter;
use App\Migrations\Factory\ContainerAwareInterface;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;

/**
 * Auto-generated Migrations: Please modify to your needs!
 */
final class Version20240108681555 extends AbstractMigration implements ContainerAwareInterface
{

    private ContainerInterface $container;

    public function getDescription(): string
    {
        return 'Load preset with drom request';
    }

    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    public function up(Schema $schema): void
    {
//        $parameters = [
//            'recSysDeviceId' => '8d07d92c740264189e0bf99976f1aa4d',
//            'recSysRegionId' => '22',
//            'recSysCityId' => '11',
//            'app_id' => 'p32',
//            'deviceId' => '8d07d92c740264189e0bf99976f1aa4d',
//            'mainPhotoWidth' => 'original',
//            'timestamp' => '{{:timestamp}}',
//            'secret' => '{{:secret}}',
//        ];
//        $url = 'https://api.drom.ru/v1.3/mycars/fetch';

//        ->parameter("revertSort", "1")


        $parameters = [
            'recSysDeviceId' => '8d07d92c740264189e0bf99976f1aa4d',
            'recSysRegionId' => '22',
            'recSysCityId' => '11',
            'app_id' => 'p32',
            'mainPhotoWidth[]' => ['320', "original"],
            'multiselect[]' => ['9_4_16_all', '9_4_15_all'],
            'isDamaged' => '2',
            'stickyRegionId[]' => ['25'],
            'cityId[]' => ['23', '170'],
            'sortBy' => 'enterdate',
            'revertSort' => 'true',
            'unsold' => '1',
            'withoutDocuments' => '2',
            'onlyWithBulletinsCount' => 'false',
            'pretty' => 'true',
            'thumbnailsWidth[]' => ['320', '600'],
            'version' => '4',
            'withModelsCount' => 'true',

            'page' => '{{:page}}',
            'timestamp' => '{{:timestamp}}',
            'secret' => '{{:secret}}',
        ];
        $url = 'https://api.drom.ru/v1.2/bulls/search';

        $groupTag = new GroupTag();
        $groupTag->setCode('drom');

        $dataSchema = new DataSchema();
        $dataSchema
            ->setUrl($url)
            ->setName('drom plain request')
            ->setGroupTag($groupTag);

        foreach ($parameters as $key => $value) {

            if (is_array($value)) {
                foreach ($value as $subValue) {
                    $requestParameter = new RequestParameter();
                    $requestParameter
                        ->setKey($key)
                        ->setValue($subValue);

                    $dataSchema->addRequestParameter($requestParameter);
                }
                continue;
            }

            $requestParameter = new RequestParameter();
            $requestParameter
                ->setKey($key)
                ->setValue($value);

            $dataSchema->addRequestParameter($requestParameter);
        }

        $container = $this->container;

        $em = $container->get('doctrine.orm.default_entity_manager');

        $em->persist($dataSchema);
        $em->flush();
    }

    public function down(Schema $schema): void
    {
        /**
         * @var EntityManager $em
         */
        $em = $this->container->get('doctrine.orm.default_entity_manager');

        $group = $em->getRepository(GroupTag::class)->findOneBy(['code', 'drom']);

        $em->remove($group);
    }
}
