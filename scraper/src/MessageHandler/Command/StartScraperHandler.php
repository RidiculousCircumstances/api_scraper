<?php

namespace App\MessageHandler\Command;


use App\Message\Scraper\StartScraperCommand;
use App\Service\ApiScraper\ApiScraper;
use App\Service\ApiScraper\Instruction\ScraperInstructionFactory;
use App\Service\ApiScraper\ScraperMessage\Trait\ScraperMessageTrait;
use App\Service\ScraperExceptions\ScraperException;
use Doctrine\ORM\NonUniqueResultException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
class StartScraperHandler implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    use ScraperMessageTrait;

    public function __construct(
        private readonly ScraperInstructionFactory $instructionsService,
        private readonly MessageBusInterface       $eventBus,
    )
    {
    }

    /**
     * Скрапер выполняет инструкцию. Инструкция может состоять из нескольких схем:
     * главным образом это нужно для случая, когда первый запрос возвращает список айтемов,
     * и для каждого айтема нужно выполнить дополнительный запрос.
     * После инициализации скрапер возвращает контекст, через который после выполнения
     * каджого лупа инструкции можно получить сообщение/управлять скрапером.
     *
     * @throws NonUniqueResultException
     */
    public function __invoke(StartScraperCommand $parsingCommand): void
    {

        try {
            /**
             * Получить дата-объект, пригодный для парсинга
             */
            $instruction = $this->instructionsService->buildInstructionFromCommand($parsingCommand);

            $ctx = ApiScraper::init($instruction);
            $scraper = $ctx->getScraper();
            $ctx->run();

            while ($ctx->isRunning()) {

                /**
                 * если ошибка случилась где-то вовне
                 */
                if ($ctx->hasError()) {
                    $ctx->stop();
                    break;
                }

                $scraper->execInstruction();
                $msg = $ctx->getMessage();

                if (!$msg || $msg->isError() || $msg->hasSuccess()) {
                    $ctx->stop();
                }

                $this->eventBus->dispatch($msg);
            }
        } catch (ScraperException $e) {
            $this->eventBus->dispatch($this->getErrorMessage($e->getMessage(), $e->getContext()));
            return;
        }


    }

}