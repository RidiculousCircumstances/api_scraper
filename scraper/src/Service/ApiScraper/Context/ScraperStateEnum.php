<?php

namespace App\Service\ApiScraper\Context;

enum ScraperStateEnum: string
{
    case RUNNING = 'runniing';

    case STOPPED = 'stopped';
}
