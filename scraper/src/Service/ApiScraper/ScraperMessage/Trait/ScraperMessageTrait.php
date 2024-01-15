<?php

namespace App\Service\ApiScraper\ScraperMessage\Trait;

use App\Service\ApiScraper\Context\ScraperContext;
use App\Service\ApiScraper\ScraperMessage\Message\Enum\ScraperStatusesEnum;
use App\Service\ApiScraper\ScraperMessage\Message\ScraperMessage;
use App\Service\ApiScraper\ScraperMessage\Message\ScraperNotification;

trait ScraperMessageTrait
{
    public function getSuccessMessage(): ScraperNotification
    {
        return new ScraperNotification(text: 'Задача завершена. Выполнено запросов: ' . $this->ctx->getRequestsCount(), ctx: $this->ctx);
    }

    private function getErrorMessage(string $message, ScraperContext $context): ScraperMessage
    {
        $context->setScraperStatus(ScraperStatusesEnum::ERROR);

        return new ScraperMessage(
            $message,
            $context
        );
    }
}