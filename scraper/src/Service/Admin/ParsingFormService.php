<?php

namespace App\Service\Admin;

use App\Form\Parsing\StartParsingType;
use App\Repository\DataSchema\DataSchemaRepository;
use Symfony\Component\Form\FormFactoryInterface;

readonly class ParsingFormService
{

    public function __construct(private FormFactoryInterface $builder, private DataSchemaRepository $dataSchemaRepository) {}

    public function createStartParsingForm(): \Symfony\Component\Form\FormInterface
    {
        $schemas = $this->dataSchemaRepository->findAll();

         return $this->builder->createNamedBuilder('', StartParsingType::class, [
            'schemaEntities' => $schemas,
            'availableFormats' => ['csv'],
            'path' => '',
            'useProxy' => false
        ])->getForm();

    }
}