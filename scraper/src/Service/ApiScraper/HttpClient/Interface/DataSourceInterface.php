<?php

namespace App\Service\ApiScraper\HttpClient\Interface;

interface DataSourceInterface
{
    public function getUrl(): string;

    public function getBody(): mixed;

    public function getMethod(): string;

    public function getProxy(): array;

    public function getHeaders(): array;
}