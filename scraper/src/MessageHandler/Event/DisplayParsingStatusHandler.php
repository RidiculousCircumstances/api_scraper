<?php

namespace App\MessageHandler\Event;

use App\Service\ApiScraper\ScraperMessage\Message\ScraperNotification;
use App\Service\ScraperUI\ScraperStatusStore;
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
    public function __invoke(ScraperNotification $message): void
    {
        $this->statusStore->persistMessage($message);
    }
}