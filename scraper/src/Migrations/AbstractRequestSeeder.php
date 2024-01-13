<?php

namespace App\Migrations;

use App\Entity\DataSchema;
use App\Entity\RequestParameter;
use App\Entity\ResponseField;
use App\Migrations\Factory\ContainerAwareInterface;
use Doctrine\Migrations\AbstractMigration;
use Psr\Container\ContainerInterface;


abstract class AbstractRequestSeeder extends AbstractMigration implements ContainerAwareInterface
{

    protected static ContainerInterface $container;

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container): void
    {
        self::$container = $container;
    }

    public function createRequest(array $parameters, DataSchema $schema, callable|null $specificLoopHandler = null, callable|null $specificPersistHandler = null): void
    {

        foreach ($parameters as $key => $value) {
            $requestParameter = new RequestParameter();

            if (is_array($value)) {
                foreach ($value as $subValue) {
                    $requestParameter = new RequestParameter();
                    $requestParameter
                        ->setKey($key)
                        ->setValue($subValue);

                    $schema->addRequestParameter($requestParameter);
                }
                continue;
            }

            if ($specificLoopHandler !== null) {
                $specificLoopHandler($key, $value, $requestParameter);
            }

            $requestParameter
                ->setKey($key)
                ->setValue((string)$value);

            $schema->addRequestParameter($requestParameter);

        }

        $em = static::$container->get('doctrine.orm.default_entity_manager');

        $em->persist($schema);

        if ($specificPersistHandler !== null) {
            $specificPersistHandler($em);
        }

        $em->flush();
    }

    public function createResponse(array $fields, DataSchema $schema): void
    {

        $em = self::$container->get('doctrine.orm.default_entity_manager');

        foreach ($fields as $path => $name) {
            $responseField = new ResponseField();
            $responseField
                ->setDataPath($path)
                ->setOutputName($name)
                ->setDataSchema($schema);
            $em->persist($responseField);
        }

        $em->flush();
    }

}