<?php

namespace App\Service\ResponseParser;


use App\Service\ApiScraper\ScraperMessage\Message\ScraperMessage;
use App\Service\ResponseParser\Instruction\DTO\ParserInstruction;
use App\Service\ResponseParser\ResponseMapper\DTO\WritableRowData;
use App\Service\ResponseParser\ResponseMapper\ResponseMapper;
use App\Service\StringPathExplorer\StringPathExplorer;

readonly class ResponseParser
{

    private ScraperMessage $message;

    private ParserInstruction $instruction;

    private ResponseMapper $mapper;

    private function __construct()
    {
        $this->mapper = new ResponseMapper(new StringPathExplorer());
    }

    public static function scraperMessage(ScraperMessage $message): self
    {
        $static = new static();
        $static->message = $message;
        return $static;
    }

    public function instruction(ParserInstruction $instruction): static
    {
        $this->instruction = $instruction;
        return $this;
    }

    public function parse(): WritableRowData
    {
        return $this->mapper->mapResponseToWritableRows($this->instruction, $this->message);
    }
}