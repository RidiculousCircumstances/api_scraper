<?php

namespace App\Service\ApiScraper\Context;

use App\Service\ApiScraper\ScraperClient\ScraperClient;
use App\Service\ApiScraper\ScraperClient\ScraperMessage;

class ScraperContext
{

    private ScraperStateEnum $state = ScraperStateEnum::RUNNING;

    private ScraperClient $scraper;

    private ScraperMessage|null $message = null;

    private int|null $iterationNumber = null;

    public function getState(): ScraperStateEnum
    {
        return $this->state;
    }

    public function setState(ScraperStateEnum $state): void
    {
        $this->state = $state;
    }

    public function getScraper(): ScraperClient
    {
        return $this->scraper;
    }

    public function setScraper(ScraperClient $scraper): self
    {
        $this->scraper = $scraper;
        return $this;
    }

    public function getMessage(): ScraperMessage|null
    {
        return $this->message;
    }

    public function setMessage(ScraperMessage|null $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getIterationNumber(): int|null
    {
        return $this->iterationNumber;
    }

    public function setIterationNumber(int|null $iterationNumber): self
    {
        $this->iterationNumber = $iterationNumber;
        return $this;
    }


}