<?php

namespace App\Service\ApiScraper\ScraperMessage\Trait;

use App\Service\ApiScraper\Context\ScraperContext;
use App\Service\ApiScraper\ScraperMessage\Message\Enum\ScraperStatusesEnum;
use App\Service\ApiScraper\ScraperMessage\Message\ScraperMessage;

trait ScraperMessageTrait
{
    private function getErrorMessage(string $message, ScraperContext $context): ScraperMessage
    {
        $context->setScraperStatus(ScraperStatusesEnum::ERROR);

        return new ScraperMessage(
            $message,
            $context
        );
    }
}