<?php

namespace App\Service\ParserResultStorage\FileWriter;

use App\Service\ParserResultStorage\FileWriter\Interface\FileWriterInterface;
use App\Service\ResponseParser\ResponseMapper\DTO\WritableRowData;

class CsvFileWriter implements FileWriterInterface
{

    /**
     * @param string $fileName
     * @param string $baseFilePath
     * Задаётся в.env - путь до файла
     * @param bool $firstResponse
     */
    public function __construct(private readonly string $fileName, private readonly string $baseFilePath, private bool $firstResponse)
    {
    }

    public function write(WritableRowData $rowData): void
    {

        if (!file_exists($this->baseFilePath) && !mkdir($this->baseFilePath) && !is_dir($this->baseFilePath)) {
            throw new \RuntimeException(sprintf('Не удалось создать директорию "%s" для сохранения изображений', $this->baseFilePath));
        }

        $resource = fopen($this->baseFilePath . $this->fileName, 'ab+');

        if ($this->firstResponse) {
            $header = $rowData->getHeaders();
            fputcsv($resource, $header);
            $this->firstResponse = false;
        }

        $row = $rowData->getRow();
        while ($row !== null) {
            fputcsv($resource, $row);
            $row = $rowData->getRow();
        }

        fclose($resource);
    }
}