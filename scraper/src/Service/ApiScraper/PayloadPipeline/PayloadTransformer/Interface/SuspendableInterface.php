<?php

namespace App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\Interface;

interface SuspendableInterface
{
    public function suspended(bool $suspended): void;

    public function isSuspended(): bool;
}