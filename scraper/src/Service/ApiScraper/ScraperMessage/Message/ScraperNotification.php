<?php

namespace App\Service\ApiScraper\ScraperMessage\Message;

use App\Service\ApiScraper\Context\ScraperContext;
use App\Service\ApiScraper\ScraperMessage\Message\Enum\ScraperStatusesEnum;

class ScraperNotification
{

    private string $dateTime;


    public function __construct(
        private string         $text,
        private ScraperContext $ctx
    )
    {
        $this->dateTime = date('Y-m-d H:i:s');
    }

    public function hasSuccess(): bool|null
    {
        return $this->ctx->getScraperStatus() === ScraperStatusesEnum::SUCCESS;
    }

    public function isError(): bool
    {
        return $this->ctx->getScraperStatus() === ScraperStatusesEnum::ERROR;
    }

    public function getDateTime(): string
    {
        return $this->dateTime;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    public function getCtx(): ScraperContext
    {
        return $this->ctx;
    }

}