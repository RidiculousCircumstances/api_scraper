<?php

namespace App\Service\ApiScraper\Context;

use App\Service\ApiScraper\ScraperClient\Interface\ApiScraperClientInterface;
use App\Service\ApiScraper\ScraperClient\ScraperMessage;

class ScraperContext
{

    private ScraperStateEnum $state = ScraperStateEnum::RUNNING;

    private ApiScraperClientInterface $scraper;

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

    public function getScraper(): ApiScraperClientInterface
    {
        return $this->scraper;
    }

    public function setScraper(ApiScraperClientInterface $scraper): self
    {
        $this->scraper = $scraper;
        return $this;
    }

    public function getMessage(): ScraperMessage|null
    {
        $msg = $this->message;
        $this->message = null;
        return $msg;
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