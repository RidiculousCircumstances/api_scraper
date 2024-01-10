<?php

namespace App\Service\ApiScraper\PayloadPipe\PayloadTransformer\Interface;

interface SuspendableInterface
{
    public function suspended(bool $suspended): void;

    public function isSuspended(): bool;
}