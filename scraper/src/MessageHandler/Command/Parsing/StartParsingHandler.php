<?php

namespace App\MessageHandler\Command\Parsing;


use App\Message\Parsing\StartParsingCommand;
use App\Service\Parsing\Client\DataSourceAdapter;
use App\Service\Parsing\Client\Interface\ClientInterface;
use App\Service\Parsing\Instruction\PrepareParsingInstructionService;
use App\Service\Parsing\ResponseRegistry\ResponseRecord;
use App\Service\Parsing\ResponseRegistry\ResponseRegistry;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class StartParsingHandler implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function __construct(
        private readonly PrepareParsingInstructionService $instructionsService,
        private readonly ClientInterface                  $client,
    )
    {
    }

    public function __invoke(StartParsingCommand $parsingCommand): void
    {
        /**
         * Получить дата-объект, пригодный для парсинга
         */
        $instructionStack = $this->instructionsService->prepareParsingInstruction($parsingCommand);
        $instruction = $instructionStack->pop();

        $registry = new ResponseRegistry();
        while ($instruction) {
            $source = new DataSourceAdapter($instruction, $registry);
            $response = $this->client->requestSource($source);
            $registry->add(new ResponseRecord(
                requestId: $instruction->getFqcn(),
                content: $response
            ));
            $instruction = $instructionStack->pop();
        }

        $a = 1;
        /**
         * перед отправкой запроса в registry сохраняется id запроса.
         * затем сохраняется контент ответа
         *
         * зависимый запрос содержит идентификатор связанного запроса
         * получаем данные по id, извлекаем по пути в value, подставляем
         *
         */

    }

}