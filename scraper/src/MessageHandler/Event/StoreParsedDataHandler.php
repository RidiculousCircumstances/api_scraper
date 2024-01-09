<?php

namespace App\MessageHandler\Event;

use App\Service\ApiScraper\ScraperClient\ScraperMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class StoreParsedDataHandler
{
    public function __invoke(ScraperMessage $message): void
    {
        $a = 1;
    }
}