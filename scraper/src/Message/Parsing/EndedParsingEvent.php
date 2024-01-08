<?php

namespace App\Message\Parsing;

readonly class EndedParsingEvent
{

    /**
     * @param string $url
     * @param string $message
     * @param string $time
     */
    public function __construct(
        private string $url,
        private string $message,
        private string $time
    )
    {
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getTime(): string
    {
        return $this->time;
    }

}