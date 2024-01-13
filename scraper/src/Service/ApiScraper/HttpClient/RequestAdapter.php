<?php

namespace App\Service\ApiScraper\HttpClient;

use App\Service\ApiScraper\HttpClient\Interface\DataSourceInterface;
use App\Service\ApiScraper\Instruction\DTO\ParsingSchemaData;

final class RequestAdapter implements DataSourceInterface
{

    public function __construct(
        private string|null $url = null,
        private string|null $method = null,
        private mixed       $body = null,
        private array|null  $proxy = null,
        private array       $headers = []
    )
    {
    }

    public static function schema(ParsingSchemaData $data): self
    {

        $requestData = $data->getRequestData();

        return new self(
            url: $requestData->getTargetUrl(),
            method: $requestData->getHttpMethod()->value
        );
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getBody(): mixed
    {
        return $this->body;
    }

    public function getProxy(): array
    {
        return $this->proxy;
    }

    public function setUrl(string|null $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function setMethod(string|null $method): self
    {
        $this->method = $method;
        return $this;
    }

    public function setBody(mixed $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function setProxy(array|null $proxy): void
    {
        $this->proxy = $proxy;
    }


    public function setHeaders(array|null $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}