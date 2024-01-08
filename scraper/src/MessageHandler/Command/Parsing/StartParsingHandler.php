<?php

namespace App\MessageHandler\Command\Parsing;


use App\Message\Parsing\EndedParsingEvent;
use App\Message\Parsing\StartParsingCommand;
use App\Service\Parsing\Client\Interface\ClientInterface;
use App\Service\Parsing\Client\RequestAdapter;
use App\Service\Parsing\Client\RequestPayloadBuilder\RequestPayloadBuilderFactory;
use App\Service\Parsing\Instruction\PrepareParsingInstructionService;
use App\Service\Parsing\PayloadPipe\PayloadTransformer\ExternalValueLoader;
use App\Service\Parsing\PayloadPipe\PayloadTransformer\PayloadSigner\PayloadSigner;
use App\Service\Parsing\PayloadPipe\PayloadTransformer\TimeStamper;
use App\Service\Parsing\PayloadPipe\PayloadTransformPipe;
use App\Service\Parsing\ResponseRegistry\ResponseRecord;
use App\Service\Parsing\ResponseRegistry\ResponseRegistry;
use Psr\Http\Client\ClientExceptionInterface;
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
        private readonly ClientInterface                  $client,
        private readonly MessageBusInterface              $eventBus,
    )
    {
    }

    public function __invoke(StartParsingCommand $parsingCommand): void
    {
        /**
         * Получить дата-объект, пригодный для парсинга
         */
        $instructionStack = $this->instructionsService->prepareParsingInstruction($parsingCommand);

        /**
         *Выполнить один цикл запросов. Нужно повторить для каждой страницы пагинации
         */
        $registry = new ResponseRegistry();
        while (!$instructionStack->isEmpty()) {

            $instruction = $instructionStack->pop();

            $requestData = $instruction->getRequestData();

            $payload = PayloadTransformPipe::payload($requestData)
                ->add(new TimeStamper())
                ->add(new PayloadSigner($instruction->getSecret()))
                ->add(new ExternalValueLoader($registry))
                ->exec();

            $payloadBuilder = RequestPayloadBuilderFactory::getBuilder($instruction->getMethod());
            $request = RequestAdapter::from($instruction)
                ->setBody($payloadBuilder->build($payload));
            try {
                $response = $this->client->requestSource($request);
            } catch (ClientExceptionInterface $exception) {
//                $msg = $exception->getResponse()->getBody()->getContents();
//                $a = json_decode($msg, true, 512, JSON_THROW_ON_ERROR);
//                $aa = 1;
                /**
                 *TODO:Реализовать слушателя с выводом результата в ui
                 */
                $this->eventBus->dispatch(new EndedParsingEvent(
                    url: $instruction->getRequestData()->getTargetUrl(),
                    message: $exception->getMessage(),
                    time: time()
                ));
                return;
            }
            $registry->add(new ResponseRecord(
                requestId: $instruction->getFqcn(),
                content: $response
            ));
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