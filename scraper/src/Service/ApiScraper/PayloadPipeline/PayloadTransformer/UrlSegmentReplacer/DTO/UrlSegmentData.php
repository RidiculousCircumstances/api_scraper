<?php

namespace App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\UrlSegmentReplacer\DTO;

readonly class UrlSegmentData
{

    public function __construct(
        private string $fullCode,
        private string $id
    )
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFullCode(): string
    {
        return $this->fullCode;
    }


}