<?php

namespace App\Service\ApiScraper\Context;

enum ScraperStateEnum: string
{
    case RUNNING = 'running';

    case STOPPED = 'stopped';
}
