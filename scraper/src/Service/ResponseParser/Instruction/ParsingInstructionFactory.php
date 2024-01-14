<?php

namespace App\Service\ResponseParser\Instruction;

use App\Entity\DataSchema;
use App\Entity\GroupTag;
use App\Entity\OutputSchema;
use App\Repository\GroupTagRepository;
use App\Repository\OutputSchema\OutputSchemaRepository;
use App\Service\ApiScraper\ScraperMessage\Message\ScraperMessage;
use App\Service\ApiScraper\ScraperMessage\Trait\ScraperMessageTrait;
use App\Service\ResponseParser\Instruction\DTO\ExtractPathData;
use App\Service\ResponseParser\Instruction\DTO\ParserInstruction;
use App\Service\ScraperExceptions\ScraperException;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Извлекает схему парсинга для полученного фрейма данных
 */
readonly class ParsingInstructionFactory
{

    use ScraperMessageTrait;

    public function __construct(
        private OutputSchemaRepository $outputSchemaRepository,
        private GroupTagRepository     $groupTagRepository,
        private MessageBusInterface    $eventBus)
    {

    }

    /**
     * @param ScraperMessage $message
     * @return ParserInstruction
     * @throws NonUniqueResultException
     * @throws ScraperException
     */
    public function getParserInstructionFromMessage(ScraperMessage $message): ParserInstruction
    {
        $ctx = $message->getCtx();
        $groupTag = $this->groupTagRepository->findByCode($ctx->getTag());

        if (!$groupTag instanceof GroupTag) {
            throw new ScraperException('[ParsingInstructionFactory] Тег группы не найден.', $ctx);
        }

        $outputSchema = $this->outputSchemaRepository->findOneByGroup($groupTag);

        if (!$outputSchema instanceof OutputSchema) {
            throw new ScraperException('[ParsingInstructionFactory] Схема парсинга не найдена.', $ctx);
        }

        $responseFields = $outputSchema->getResponseFields();

        $instruction = $ctx->getInstruction();
        $parsingConfig = $instruction->getParsingConfig();

        $extractResponseInstruction = new ParserInstruction($parsingConfig);
        foreach ($responseFields as $responseField) {

            $dataSchema = $responseField->getDataSchema();

            if (!$dataSchema instanceof DataSchema) {
                throw new ScraperException('[ParsingInstructionFactory] Не удалось определить идентификатор запроса.', $ctx);
            }

            $requestId = $dataSchema->getFqcn();
            $outputName = $responseField->getOutputName();

            $extractResponseInstruction->addExtractPath(new ExtractPathData($responseField->getDataPath(), $requestId, $outputName));
        }


        return $extractResponseInstruction;

    }


}