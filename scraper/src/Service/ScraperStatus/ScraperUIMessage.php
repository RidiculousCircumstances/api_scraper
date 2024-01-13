<?php

namespace App\Service\ScraperStatus;

class ScraperUIMessage
{
    private string $message;

    private bool $isError;

    /**
     * @param string $message
     * @param bool $isError
     */
    public function __construct(string $message, bool $isError)
    {
        $this->message = $message;
        $this->isError = $isError;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function isError(): bool
    {
        return $this->isError;
    }


}