<?php

namespace App\Service\ParserResultStorage;

use App\Message\Scraper\Enum\OutputFormatsEnum;
use App\Service\ApiScraper\Instruction\DTO\ParsingConfigData;
use App\Service\ParserResultStorage\FileWriter\CsvFileWriter;
use App\Service\ParserResultStorage\FileWriter\Interface\FileWriterInterface;
use App\Service\ResponseParser\ResponseMapper\DTO\WritableRowData;

/**
 * Сохраняет резальтаты парсинга
 */
class ParserResultStorage
{

    private ParsingConfigData $config;
    private bool $isFirstResponse;

    public static function config(ParsingConfigData $context, bool $isFirstResponse): static
    {
        $static = new static;
        $static->config = $context;
        $static->isFirstResponse = $isFirstResponse;
        return $static;
    }

    public function store(WritableRowData $rowData): void
    {
        $fileWriter = $this->resolveWriter();
        $fileWriter->write($rowData);
    }

    private function resolveWriter(): FileWriterInterface
    {
        $config = $this->config;

        return match ($config->getOutputFormat()) {
            OutputFormatsEnum::CSV => new CsvFileWriter($config->getFilePath(), $config->getBaseFilePath(), $this->isFirstResponse)
        };
    }
}