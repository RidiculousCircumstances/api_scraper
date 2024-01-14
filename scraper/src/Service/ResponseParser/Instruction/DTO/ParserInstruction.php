<?php

namespace App\Service\ResponseParser\Instruction\DTO;

use App\Service\ApiScraper\Instruction\DTO\ParsingConfigData;

/**
 * Хранит данные о путях извлечения значений из полученного фрейма данных.
 * Маппится на респонс посредством идентификатора DataSchema
 */
class ParserInstruction
{

    /**
     * @var array<ExtractPathData> $extractPaths
     */
    private array $extractPaths;


    public function __construct(private readonly ParsingConfigData $parsingConfig)
    {

    }

    public function getExtractPathsByRequestId(string $requestId): array
    {
        return array_filter($this->extractPaths, static function (ExtractPathData $extractValueData) use ($requestId) {
            return $extractValueData->getRequestId() === $requestId;
        });
    }

    public function addExtractPath(ExtractPathData $extractPathData): void
    {
        $this->extractPaths[] = $extractPathData;
    }

    /**
     * @return ExtractPathData[]
     */
    public function getExtractPaths(): array
    {
        return $this->extractPaths;
    }

    public function getParsingConfig(): ParsingConfigData
    {
        return $this->parsingConfig;
    }

}