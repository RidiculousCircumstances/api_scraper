<?php

namespace App\Service\ApiScraper\ScraperClient;

class ScraperMessage
{
    public function __construct(
        private                 $message,
        private readonly string $url,
        private bool            $isError = false,

    )
    {
    }

    /**
     * @return mixed
     */
    public function getMessage(): mixed
    {
        return $this->message;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function isError(): bool
    {
        return $this->isError;
    }


}