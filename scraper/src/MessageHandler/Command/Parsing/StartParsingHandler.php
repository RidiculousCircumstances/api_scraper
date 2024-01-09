<?php

namespace App\MessageHandler\Command\Parsing;


use App\Message\Parsing\StartParsingCommand;
use App\Service\ApiScraper\ApiScraper;
use App\Service\ApiScraper\Context\ScraperStateEnum;
use App\Service\ApiScraper\Instruction\PrepareParsingInstructionService;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
class StartParsingHandler implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly PrepareParsingInstructionService $instructionsService,
        private readonly MessageBusInterface              $eventBus,
    )
    {
    }

    public function __invoke(StartParsingCommand $parsingCommand): void
    {

        /**
         * Получить дата-объект, пригодный для парсинга
         */
        $instruction = $this->instructionsService->prepareParsingInstruction($parsingCommand);

        $ctx = ApiScraper::init($instruction);

        while ($ctx->getState() === ScraperStateEnum::RUNNING) {
            $scraper = $ctx->getScraper();
            $scraper->sendRequest();
            $msg = $ctx->getMessage();

            if (!$msg || $msg->isError()) {
                $ctx->setState(ScraperStateEnum::STOPPED);
            }

            $this->eventBus->dispatch($msg);
        }

    }

}