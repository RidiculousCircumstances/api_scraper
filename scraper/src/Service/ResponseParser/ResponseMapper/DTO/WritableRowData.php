<?php

namespace App\Service\ResponseParser\ResponseMapper\DTO;

class WritableRowData
{

    /**
     * @var array
     */
    private array $rowElements;

    public function addRowElement(string $outputName, string|null $value): void
    {
        $this->rowElements[$outputName][] = $value;
    }

    public function getRow(): array|null
    {
        $headers = $this->getHeaders();

        $row = [];

        foreach ($headers as $header) {
            $row[] = array_pop($this->rowElements[$header]);
        }

        if (count(array_filter($row)) === 0) {
            return null;
        }

        return $row;

    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return array_keys($this->rowElements);
    }

}