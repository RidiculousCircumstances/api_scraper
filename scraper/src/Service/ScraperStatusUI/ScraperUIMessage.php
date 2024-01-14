<?php

namespace App\Service\ScraperStatusUI;

use App\Service\ApiScraper\ScraperMessage\Message\Enum\ScraperStatusesEnum;

readonly class ScraperUIMessage
{


    private string $message;

    /**
     * @param string $message
     * @param ScraperStatusesEnum $status
     * @param string $dateTime
     */
    public function __construct($message, private ScraperStatusesEnum $status, private string $dateTime)
    {
        if (!is_string($message)) {
            $this->message = serialize($message);
            return;
        }
        $this->message = $message;
    }

    public function getMessage(): string
    {
        return $this->message;
    }


    public function getDateTime(): string
    {
        return $this->dateTime;
    }

    public function getStatus(): ScraperStatusesEnum
    {
        return $this->status;
    }

}