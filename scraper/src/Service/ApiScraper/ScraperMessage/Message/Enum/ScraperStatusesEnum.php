<?php

namespace App\Service\ApiScraper\ScraperMessage\Message\Enum;

enum ScraperStatusesEnum: int
{
    case SUCCESS = 0;

    case ERROR = 1;

    case PROCESS = 2;

    case PENDING = 3;
}
