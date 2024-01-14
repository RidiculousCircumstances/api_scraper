<?php

namespace App\Service\Admin;

use App\Form\Scraper\ControlPanelType;
use App\Message\Scraper\Enum\HttpMethodsEnum;
use App\Message\Scraper\Enum\OutputFormatsEnum;
use App\Repository\OutputSchema\OutputSchemaRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

readonly class ControlPanelFormService
{

    public function __construct(private FormFactoryInterface $builder, private OutputSchemaRepository $outputSchemaRepository)
    {
    }

    public function createStartParsingForm(): FormInterface
    {
        $schemas = $this->outputSchemaRepository->findAll();

        return $this->builder->createNamedBuilder('', ControlPanelType::class, [
            'schemaEntities' => $schemas,
            'availableFormats' => [OutputFormatsEnum::CSV],
            'availableMethods' => [
                HttpMethodsEnum::GET->value,
            ],
            'secret' => '',
            'auth' => '',
            'file' => 'drom.csv',
            'useProxy' => false,
            'delay' => 500
        ])->getForm();

    }
}