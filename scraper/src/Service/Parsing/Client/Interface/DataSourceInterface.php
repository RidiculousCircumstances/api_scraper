<?php

namespace App\Service\Parsing\Client\Interface;

interface DataSourceInterface
{
    public function getUrl(): string;

    public function getBody(): array;

    public function getMethod(): string;

    public function getProxy(): array;
}