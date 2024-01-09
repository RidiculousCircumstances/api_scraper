<?php

namespace App\Service\ApiScraper\Instruction\DTO;

/**
 * Параметру запроса может соответствовать значение, получаемое из другого связанного запроса,
 * поэтому в дата объект помещаетсся ссылка на внешнюю инструкцию
 */
class RequestParameterData
{
    public function __construct(
        private readonly string $key,

        private readonly mixed  $value,

        private string|null     $externalSourceId = null,
    )
    {
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getExternalSourceId(): string|null
    {
        return $this->externalSourceId;
    }

    public function setExternalSourceId(string $instructionData): static
    {
        $this->externalSourceId = $instructionData;
        return $this;
    }

}