<?php

namespace App\MessageHandler\Event;

use App\Service\ApiScraper\ScraperMessage\Message\ScraperMessage;
use App\Service\ScraperStatusUI\ScraperStatusStore;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Сохраняет сообщения скрапера, которые выводятся на панель управления.
 */
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