<?php

namespace App\Service\Admin;

use App\Form\Scraper\StartScrapingType;
use App\Message\Parsing\Enum\HttpMethodsEnum;
use App\Repository\DataSchema\DataSchemaRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

readonly class ParsingFormService
{

    public function __construct(private FormFactoryInterface $builder, private DataSchemaRepository $dataSchemaRepository)
    {
    }

    public function createStartParsingForm(): FormInterface
    {
        $schemas = $this->dataSchemaRepository->findAll();

        return $this->builder->createNamedBuilder('', StartScrapingType::class, [
            'schemaEntities' => $schemas,
            'availableFormats' => ['csv'],
            'availableMethods' => [
                HttpMethodsEnum::GET->value,
                HttpMethodsEnum::POST->value
            ],
            'secret' => '',
            'auth' => '',
            'path' => '/home',
            'useProxy' => false,
            'delay' => 500
        ])->getForm();

    }
}