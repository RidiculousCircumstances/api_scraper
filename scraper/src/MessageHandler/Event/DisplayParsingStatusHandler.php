<?php

namespace App\MessageHandler\Event;

use App\Service\ApiScraper\ScraperClient\ScraperMessage;
use App\Service\ScraperStatus\ScraperStatusStore;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class DisplayParsingStatusHandler
{

    public function __construct(private ScraperStatusStore $statusStore)
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function __invoke(ScraperMessage $message): void
    {
        $this->statusStore->persistMessage($message);
    }
}