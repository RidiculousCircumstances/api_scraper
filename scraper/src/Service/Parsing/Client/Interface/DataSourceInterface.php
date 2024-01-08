<?php

namespace App\Service\Parsing\Client\Interface;

interface DataSourceInterface
{
    public function getUrl(): string;

    public function getBody(): mixed;

    public function getMethod(): string;

    public function getProxy(): array;
}