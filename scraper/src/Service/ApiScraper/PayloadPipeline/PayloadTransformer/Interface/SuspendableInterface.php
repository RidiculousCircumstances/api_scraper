<?php

namespace App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\Interface;

interface SuspendableInterface
{
    public function setSuspended(bool $suspended): void;

    public function isSuspended(): bool;
}