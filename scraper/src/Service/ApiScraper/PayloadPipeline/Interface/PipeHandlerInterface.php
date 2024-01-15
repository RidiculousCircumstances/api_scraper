<?php

namespace App\Service\ApiScraper\PayloadPipeline\Interface;

use App\Service\ApiScraper\Instruction\DTO\RequestData;

interface PipeHandlerInterface
{

    /**
     * @param RequestData $requestData
     * @return void
     */
    public function transform(RequestData $requestData): void;
}