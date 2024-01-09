<?php

namespace App\Service\ApiScraper;

use App\Service\ApiScraper\Context\ScraperContext;
use App\Service\ApiScraper\HttpClient\Client;
use App\Service\ApiScraper\Instruction\DTO\ScraperInstructionData;
use App\Service\ApiScraper\ScraperClient\ScraperClient;
use App\Service\ApiScraper\ScraperClient\SuccessRecognizer\DifferenceSuccessRecognizer;

class ApiScraper
{

    public static function init(ScraperInstructionData $instruction): ScraperContext
    {

        $ctx = new ScraperContext();

        $client = new ScraperClient($ctx, $instruction, new Client(), new DifferenceSuccessRecognizer());

        $ctx->setScraper($client);

        return $ctx;
    }
}