<?php

namespace App\Service\ScraperUI;

use App\Service\ApiScraper\ScraperMessage\Message\ScraperNotification;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

class ScraperStatusStore
{
    private const SCRAPER_MESSAGE_KEY = 'scraper_message';

    public function __construct(private readonly CacheItemPoolInterface $cachePool)
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function persistMessage(ScraperNotification $notification): void
    {
        $messageCache = $this->cachePool->getItem(self::SCRAPER_MESSAGE_KEY);
        $messageCache->set($this->buildMessage($notification));
        $this->cachePool->save($messageCache);
    }

    private function buildMessage(ScraperNotification $message): ScraperUIMessage
    {
        $status = $message->getCtx()->getScraperStatus();
        return new ScraperUIMessage($message->getText(), $status, $message->getDateTime());

    }

    /**
     * @throws InvalidArgumentException
     */
    public function getScraperUIMessage(): \Psr\Cache\CacheItemInterface
    {
        return $this->cachePool->getItem(self::SCRAPER_MESSAGE_KEY);
    }

}