<?php

namespace App\Service\ApiScraper\ScraperClient;

use App\Service\ApiScraper\Context\ScraperContext;
use App\Service\ApiScraper\HttpClient\Exceptions\HttpClientException;
use App\Service\ApiScraper\HttpClient\Interface\ClientInterface;
use App\Service\ApiScraper\Instruction\Instruction\ScraperInstruction;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\ExternalValueLoader\ExternalValueLoader;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\FieldsRandomizer\FieldsRandomizer;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\PageIncrementor;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\PayloadSigner\PayloadSigner;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\TimeStamper;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\UrlSegmentReplacer\UrlSegmentReplacer;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformPipe;
use App\Service\ApiScraper\ResponseBag\ResponseBag;
use App\Service\ApiScraper\ResponseBag\ResponseRecord;
use App\Service\ApiScraper\ScraperClient\Interface\ApiScraperClientInterface;
use App\Service\ApiScraper\ScraperClient\SuccessRecognizer\Interface\RecognizerInterface;
use App\Service\ApiScraper\ScraperMessage\Message\Enum\ScraperStatusesEnum;
use App\Service\ApiScraper\ScraperMessage\Message\ScraperMessage;
use App\Service\ApiScraper\ScraperMessage\Message\ScraperNotification;
use App\Service\ApiScraper\ScraperMessage\Trait\ScraperMessageTrait;
use RuntimeException;

final class ScraperClient implements ApiScraperClientInterface
{


    use ScraperMessageTrait;

    /**
     * Максимальное количество неудачных попыток
     */
    public const RETRIES = 2;

    /**
     * Текущее количество неудач
     * @var int
     */
    private int $failuresCount = 0;

    /**
     *Флаг принудительного завершения программмы
     * @var bool
     */
    private bool $terminate = false;

    private bool $pending = true;

    private bool $firstResponse = true;

    public function __construct(
        private readonly ScraperContext      $ctx,
        private readonly ScraperInstruction  $instruction,
        private readonly ClientInterface     $httpClient,
        private readonly RecognizerInterface $successRecognizer,
        private readonly RecognizerInterface $badOutcomeRecognizer
    )
    {
    }

    public function execInstruction(): void
    {

        if ($this->terminate) {
            throw new RuntimeException('[ScraperClient] Превышен лимит неудачных запросов.');
        }

        /**
         * Установка флага "не является первым запрососм" в контекст
         */
        if ($this->firstResponse) {
            $this->firstResponse = false;
        } else {
            $this->ctx->setIsFirstResponse(false);
        }

        $responseBag = new ResponseBag();
        $instruction = $this->instruction;

        $instruction->rewind();

        if ($this->pending) {
            $this->ctx
                ->setScraperStatus(ScraperStatusesEnum::PROCESS);
            $this->pending = false;
        }

        while (!$instruction->isExecuted()) {

            $this->ctx->addRequestsCount();
            $notify = new ScraperNotification(text: 'Идёт обработка. Обработано запросов: ' . $this->ctx->getRequestsCount(), ctx: $this->ctx);
            $this->ctx->setNotification($notify);

            $requestConfig = $instruction->getRequestConfig();

            usleep($requestConfig->getDelay());

            $schemaData = $instruction->extractSchema();

            /**
             * Если инструкция состоит более чем из одной схемы, и главная схема не вернула для связанной
             * данные, значит, все итерации заершены
             */
            $schemaData = PayloadTransformPipe::payload($schemaData)
                ->with(new TimeStamper())
                ->with(ExternalValueLoader::new($responseBag, $instruction))
                ->with(new UrlSegmentReplacer())
                ->with(new PageIncrementor($this->ctx))
                ->with(new FieldsRandomizer())
                ->with(new PayloadSigner($requestConfig->getSecret()))
                ->transform();

            if (ExternalValueLoader::mainRequestReturnedEmptyData()) {
                $notify = $this->getSuccessMessage();
                $this->ctx
                    ->setScraperStatus(ScraperStatusesEnum::SUCCESS)
                    ->setNotification($notify);
                return;
            }

            /**
             * Производим манипуляции с полезной нагрузкой e.g.загружаем внешние значения, подставляем значения.
             */
            $request = RequestFactory::getRequest($instruction->getRequestConfig(), $schemaData);

            try {

                $response = $this->httpClient->request($request);

                /**
                 * Собираем все респонсы в рамках инструкции, затем отправляем в контекст
                 */
                $responseBag->addResponseRecord(new ResponseRecord(
                    requestId: $schemaData->getFqcn(),
                    content: $response,
                ));

                if (!$instruction->isExecuted()) {
                    continue;
                }

                $msg = new ScraperMessage(
                    payload: $responseBag,
                    ctx: $this->ctx,
                );

                if ($this->successRecognizer->recognize($response)) {
                    $notify = $this->getSuccessMessage();
                    $this->ctx
                        ->setScraperStatus(ScraperStatusesEnum::SUCCESS)
                        ->setNotification($notify);
                    return;
                }

                $this->ctx->setMessage($msg);

            } catch (HttpClientException $exception) {

                /**
                 * Если количество неудачных запросов превысило лимит, и клиентский код по какой-то причине продолжает
                 * вызов, терминируем клиент, при последующем вызове будет брошена ошибка.
                 */
                if ($this->failuresCount >= self::RETRIES) {
                    $this->terminate = true;
                    break;
                }

                /**
                 *При получении http ошибки прерываемся после как минимум двух неудач
                 */
                $msg = $exception->getMessage();

                sleep(1);

                $instruction->repeatLastSchema();

                $this->failuresCount++;

                if (!$this->badOutcomeRecognizer->recognize($exception->getCode())) {
                    continue;
                }
                $errorMsg = new ScraperNotification(text: $msg, ctx: $this->ctx);
                $this->ctx
                    ->setScraperStatus(ScraperStatusesEnum::ERROR)
                    ->setNotification($errorMsg);
            }
        }
    }
}