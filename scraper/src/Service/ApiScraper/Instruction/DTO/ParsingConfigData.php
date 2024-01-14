<?php

namespace App\Service\ApiScraper\Instruction\DTO;

use App\Message\Scraper\Enum\OutputFormatsEnum;

final class ParsingConfigData
{

    public function __construct(
        private string            $filePath,
        private OutputFormatsEnum $outputFormat,
        private string            $baseFilePath,
    )
    {

    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function getOutputFormat(): OutputFormatsEnum
    {
        return $this->outputFormat;
    }

    public function getBaseFilePath(): string
    {
        return $this->baseFilePath;
    }

}