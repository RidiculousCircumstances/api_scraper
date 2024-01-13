<?php

namespace App\Service\ScraperStatus;

use App\Service\ApiScraper\ScraperClient\ScraperMessage;
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
    public function persistMessage(ScraperMessage $errorMessage): void
    {
        $messageCache = $this->cachePool->getItem(self::SCRAPER_MESSAGE_KEY);
        $messageCache->set($this->buildMessage($errorMessage));
        $this->cachePool->save($messageCache);
    }

    private function buildMessage(ScraperMessage $message): ScraperUIMessage
    {

        if ($message->isError()) {
            $msg = $message->getPayload();
            return new ScraperUIMessage($msg, true);
        }

        return new ScraperUIMessage('Задача успешно обработана', false);

    }

    /**
     * @throws InvalidArgumentException
     */
    public function getScraperUIMessage(): \Psr\Cache\CacheItemInterface
    {
        return $this->cachePool->getItem(self::SCRAPER_MESSAGE_KEY);
    }

}