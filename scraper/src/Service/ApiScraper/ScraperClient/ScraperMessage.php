<?php

namespace App\Service\ApiScraper\ScraperClient;

class ScraperMessage
{
    public function __construct(
        private           $payload,
        private string    $url,
        private bool|null $isError = null,
        private bool|null $success = null,
    )
    {
    }

    /**
     * @return mixed
     */
    public function getPayload(): mixed
    {
        return $this->payload;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function isError(): bool|null
    {
        return $this->isError;
    }

    public function hasSuccess(): bool|null
    {
        return $this->success;
    }

    /**
     * @param mixed $payload
     */
    public function setPayload($payload): void
    {
        $this->payload = $payload;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function setIsError(): void
    {
        $this->isError = true;
        $this->success = false;
    }

    public function setSuccess(): void
    {
        $this->success = true;
    }


}