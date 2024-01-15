<?php

namespace App\Service\ResponseParser;


use App\Service\ApiScraper\Instruction\DTO\ParsingConfigData;
use App\Service\ApiScraper\ScraperMessage\Message\ScraperMessage;
use App\Service\ResponseParser\Instruction\DTO\ParserInstruction;
use App\Service\ResponseParser\PostProcessPipeline\PostProcessor\ImageLoader;
use App\Service\ResponseParser\PostProcessPipeline\PostProcessPipe;
use App\Service\ResponseParser\ResponseMapper\DTO\WritableRowData;
use App\Service\ResponseParser\ResponseMapper\ResponseMapper;
use App\Service\StringPathExplorer\StringPathExplorer;

readonly class ResponseParser
{

    private ScraperMessage $message;

    private ParserInstruction $instruction;

    private ResponseMapper $mapper;

    private ParsingConfigData $config;

    private function __construct()
    {
        $this->mapper = new ResponseMapper(new StringPathExplorer());
    }

    public static function scraperMessage(ScraperMessage $message): self
    {
        $static = new static();
        $static->message = $message;
        $static->config = $message->getCtx()->getInstruction()->getParsingConfig();
        return $static;
    }

    public function instruction(ParserInstruction $instruction): static
    {
        $this->instruction = $instruction;
        return $this;
    }

    public function parse(): WritableRowData
    {
        $writableRow = $this->mapper->mapResponseToWritableRows($this->instruction, $this->message);
        $outputDirectory = $this->config->getBaseFilePath();

        $writableRow = PostProcessPipe::payload($writableRow)
            ->with(new ImageLoader($outputDirectory))
            ->transform();

        return $writableRow;
    }
}