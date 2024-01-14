<?php

namespace App\Service\ApiScraper\ScraperMessage\Message;

use App\Service\ApiScraper\Context\ScraperContext;
use App\Service\ApiScraper\ScraperMessage\Message\Enum\ScraperStatusesEnum;

class ScraperMessage
{

    private string $dateTime;


    public function __construct(
        private                $payload,
        private ScraperContext $ctx,
    )
    {
        $this->dateTime = date('Y-m-d H:i:s');
    }

    /**
     * @return mixed
     */
    public function getPayload(): mixed
    {
        return $this->payload;
    }


    public function hasSuccess(): bool|null
    {
        return $this->ctx->getScraperStatus() === ScraperStatusesEnum::SUCCESS;
    }

    public function isError(): bool
    {
        return $this->ctx->getScraperStatus() === ScraperStatusesEnum::ERROR;
    }

    /**
     * @param mixed $payload
     */
    public function setPayload($payload): void
    {
        $this->payload = $payload;
    }

    public function getDateTime(): string
    {
        return $this->dateTime;
    }

    public function getCtx(): ScraperContext
    {
        return $this->ctx;
    }

}