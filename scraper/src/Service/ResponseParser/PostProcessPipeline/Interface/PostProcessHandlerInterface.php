<?php

namespace App\Service\ResponseParser\PostProcessPipeline\Interface;

use App\Service\ResponseParser\ResponseMapper\DTO\WritableRowData;

interface PostProcessHandlerInterface
{
    public function transform(WritableRowData $writableRow): void;
}