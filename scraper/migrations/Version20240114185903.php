<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\OutputSchema;
use App\Migrations\AbstractRequestSeeder;
use App\Repository\GroupTagRepository;
use App\Repository\ResponseField\ResponseFieldRepository;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240114185903 extends AbstractRequestSeeder
{
    public function getDescription(): string
    {
        return 'Пресет аутпут схемы списка авто';
    }

    public function up(Schema $schema): void
    {
        $container = self::$container;

        /**
         * @var ResponseFieldRepository $rfRepo
         */
        $rfRepo = $container->get(ResponseFieldRepository::class);
        $gtRepo = $container->get(GroupTagRepository::class);

        $groupTag = $gtRepo->findOneBy(['code' => 'drom']);
        $responseFields = $rfRepo->findByGroupTag($groupTag);

        $em = $container->get('doctrine.orm.default_entity_manager');

        $outputSchema = new OutputSchema();
        $outputSchema->setGroupTag($groupTag)->setName('car_list_schema');

        foreach ($responseFields as $responseField) {
            $outputSchema->addResponseField($responseField);
        }

        $em->persist($outputSchema);
        $em->flush();
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
    }
}
