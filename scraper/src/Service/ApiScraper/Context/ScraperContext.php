<?php

namespace App\Service\ApiScraper\Context;

use App\Service\ApiScraper\Instruction\Instruction\ScraperInstruction;
use App\Service\ApiScraper\ScraperClient\Interface\ApiScraperClientInterface;
use App\Service\ApiScraper\ScraperMessage\Message\Enum\ScraperStatusesEnum;
use App\Service\ApiScraper\ScraperMessage\Message\ScraperMessage;

class ScraperContext
{

    private ScraperStateEnum $state = ScraperStateEnum::RUNNING;

    private ApiScraperClientInterface $scraper;

    private ScraperMessage|null $message = null;

    private int|null $iterationNumber = null;

    private string $tag = '';

    private ScraperInstruction $scraperInstruction;

    private ScraperStatusesEnum $scraperStatus = ScraperStatusesEnum::PENDING;

    private bool $isFirstResponse = true;


    public function isRunning(): bool
    {
        return $this->state === ScraperStateEnum::RUNNING;
    }

    /**
     * Установить состояние скрапера в RUNNING
     * @return void
     */
    public function run(): void
    {
        $this->state = ScraperStateEnum::RUNNING;
    }

    /**
     * Установить состояние скрапера в STOPPED
     * @return void
     */
    public function stop(): void
    {
        $this->state = ScraperStateEnum::STOPPED;
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

    public function getTag(): string
    {
        return $this->tag;
    }

    public function setTag(string $tag): self
    {
        $this->tag = $tag;
        return $this;
    }

    /**
     * Получить текущее состояние скрапера
     * @return ScraperStatusesEnum
     */
    public function getScraperStatus(): ScraperStatusesEnum
    {
        return $this->scraperStatus;
    }

    public function setScraperStatus(ScraperStatusesEnum $scraperStatus): self
    {
        $this->scraperStatus = $scraperStatus;
        return $this;
    }

    public function hasError(): bool
    {
        return $this->scraperStatus === ScraperStatusesEnum::ERROR;
    }

    public function getInstruction(): ScraperInstruction
    {
        return $this->scraperInstruction;
    }

    public function setScraperInstruction(ScraperInstruction $scraperInstruction): self
    {
        $this->scraperInstruction = $scraperInstruction;
        return $this;
    }

    public function isFirstResponse(): bool
    {
        return $this->isFirstResponse;
    }

    public function setIsFirstResponse(bool $isFirstResponse): self
    {
        $this->isFirstResponse = $isFirstResponse;
        return $this;
    }

}