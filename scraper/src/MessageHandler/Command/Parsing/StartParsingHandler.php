<?php

namespace App\MessageHandler\Command\Parsing;


use App\Message\Parsing\StartParsingCommand;
use App\Service\Parsing\PrepareParsingInstructionService;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class StartParsingHandler implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly PrepareParsingInstructionService $instructionsService
    ) {}

    public function __invoke(StartParsingCommand $parsingCommand): void
    {

        /**
         * Получить дата-объект, пригодный для парсинга
         */
        $data = $this->instructionsService->prepareParsingInstruction($parsingCommand);




    }

}