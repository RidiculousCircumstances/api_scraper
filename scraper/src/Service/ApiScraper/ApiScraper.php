<?php

namespace App\Service\ApiScraper;

use App\Service\ApiScraper\Context\ScraperContext;
use App\Service\ApiScraper\HttpClient\Client;
use App\Service\ApiScraper\Instruction\Instruction\ScraperInstruction;
use App\Service\ApiScraper\ScraperClient\ScraperClient;
use App\Service\ApiScraper\ScraperClient\SuccessRecognizer\DifferenceRecognizer;

class ApiScraper
{

    public static function init(ScraperInstruction $instruction): ScraperContext
    {

        $ctx = new ScraperContext();


        $client = new ScraperClient($ctx, $instruction, new Client(), new DifferenceRecognizer(), new DifferenceRecognizer());

        $ctx
            ->setScraper($client)
            ->setScraperInstruction($instruction)
            ->setTag($instruction->getTag());

        return $ctx;
    }
}