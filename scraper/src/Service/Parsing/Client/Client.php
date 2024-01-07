<?php

namespace App\Service\Parsing\Client;

use App\Service\Parsing\Client\Interface\ClientInterface;
use App\Service\Parsing\Client\Interface\DataSourceInterface;

class Client implements ClientInterface
{

    public function requestSource(DataSourceInterface $source): array
    {
        return [];
    }
}