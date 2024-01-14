<?php

namespace App\Service\ResponseParser\ResponseMapper;

use App\Service\ApiScraper\ResponseBag\ResponseBag;
use App\Service\ApiScraper\ScraperMessage\Message\ScraperMessage;
use App\Service\ResponseParser\Instruction\DTO\ExtractPathData;
use App\Service\ResponseParser\Instruction\DTO\ParserInstruction;
use App\Service\ResponseParser\ResponseMapper\DTO\WritableRowData;
use App\Service\ScraperExceptions\ScraperException;
use App\Service\StringPathExplorer\StringPathExplorer;

readonly class ResponseMapper
{

    public function __construct(private StringPathExplorer $explorer)
    {
    }

    /**
     * Отображает данные ответа в массив пар ключ-значение, пригодный для сохранения
     * @param ParserInstruction $instruction
     * @param ScraperMessage $message
     * @return WritableRowData
     */
    public function mapResponseToWritableRows(ParserInstruction $instruction, ScraperMessage $message): WritableRowData
    {

        $responseBag = $message->getPayload();

        if (!$responseBag instanceof ResponseBag) {
            throw new ScraperException('[ResponseMapper] Не удалось получить объект ответа для парсинга.', $message->getCtx());
        }

        $responseData = $responseBag->getResponseRecords();

        $writableRowData = new WritableRowData();

        foreach ($responseData as $responseRecord) {

            $payload = $responseRecord->getContent();

            /**
             * @var array<ExtractPathData> $fullPaths
             */
            $extractPaths = $instruction->getExtractPathsByRequestId($responseRecord->getRequestId());

            if ($payload === null) {
                throw new ScraperException('[ResponseMapper] не удалось сопоставить запрос со схемой.', $message->getCtx());
            }

            foreach ($extractPaths as $extractPath) {

                $pathString = $extractPath->getPath();

                if (!$this->explorer->checkMultipleItemsInPath($pathString)) {
                    /**
                     *Если объект плоский - каждая итерация - запись в одну строку
                     */
                    $value = $this->explorer->extractValue($pathString, $payload);
                    $writableRowData->addRowElement($extractPath->getName(), $value);
                    continue;
                }

                /**
                 *  Иначе каждая итерация - запись во все строки
                 */
                $items = $this->explorer->extractItems($pathString, $payload);
                foreach ($items as $item) {
                    $value = $this->explorer->extractValue($pathString, $item);
                    $writableRowData->addRowElement($extractPath->getName(), $value);
                }


            }
        }

        return $writableRowData;
    }
}