<?php

namespace App\MessageHandler\Event;

use App\Repository\GroupTagRepository;
use App\Service\ApiScraper\ScraperMessage\Message\Enum\ScraperStatusesEnum;
use App\Service\ApiScraper\ScraperMessage\Message\ScraperMessage;
use App\Service\ApiScraper\ScraperMessage\Trait\ScraperMessageTrait;
use App\Service\ParserResultStorage\ParserResultStorage;
use App\Service\ResponseParser\Instruction\ParsingInstructionFactory;
use App\Service\ResponseParser\ResponseParser;
use App\Service\ScraperExceptions\ScraperException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class StoreParsedDataHandler
{

    use ScraperMessageTrait;

    public function __construct(
        private GroupTagRepository        $groupTagRepository,
        private ParsingInstructionFactory $parsingInstructionFactory,
    )
    {
    }

    /**
     * @param ScraperMessage $message
     * @return void
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function __invoke(ScraperMessage $message): void
    {
        if ($message->getCtx()->getScraperStatus() !== ScraperStatusesEnum::PROCESS) {
            return;
        }

        try {
            $ctx = $message->getCtx();
            $instruction = $this->parsingInstructionFactory->getParserInstructionFromMessage($message);
            $writableRows = ResponseParser::scraperMessage($message)->instruction($instruction)->parse();

            ParserResultStorage::config($ctx->getInstruction()->getParsingConfig(), $ctx->isFirstResponse())
                ->store($writableRows);

        } catch (ScraperException $e) {
            $this->getErrorMessage($e->getMessage(), $e->getContext());
            return;
        }


    }
}