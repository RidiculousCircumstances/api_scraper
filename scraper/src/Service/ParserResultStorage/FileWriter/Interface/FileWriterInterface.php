<?php

namespace App\Service\ParserResultStorage\FileWriter\Interface;

use App\Service\ResponseParser\ResponseMapper\DTO\WritableRowData;

interface FileWriterInterface
{

    public function write(WritableRowData $rowData): void;
}