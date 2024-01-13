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
use RuntimeException;

final class ScraperClient implements ApiScraperClientInterface
{

    public const RETRIES = 2;

    private int $failuresCount = 0;

    private bool $terminate = false;

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

        $responseBag = new ResponseBag();
        $instruction = $this->instruction;

        $instruction->rewind();

        while (!$instruction->isExecuted()) {

            $requestConfig = $instruction->getRequestConfig();

            usleep($requestConfig->getDelay());

            $schemaData = $instruction->extractSchema();

            /**
             * Производим манипуляции с полезной нагрузкой e.g.загружаем внешние значения, подставляем значения.
             */
            $schemaData = PayloadTransformPipe::payload($schemaData)
                ->with(new TimeStamper())
                ->with(ExternalValueLoader::new($responseBag, $instruction))
                ->with(new UrlSegmentReplacer())
                ->with(new PageIncrementor($this->ctx))
                ->with(new FieldsRandomizer())
                ->with(new PayloadSigner($requestConfig->getSecret()))
                ->transform();

            $request = RequestFactory::getRequest($instruction->getRequestConfig(), $schemaData);

            try {

                $response = $this->httpClient->request($request);

                /**
                 * Собираем все респонсы в рамках инструкции, затем отправляем в контекст
                 */
                $responseBag->add(new ResponseRecord(
                    requestId: $schemaData->getFqcn(),
                    content: $response,
                ));

                if (!$instruction->isExecuted()) {
                    continue;
                }

                $msg = new ScraperMessage(
                    payload: $response,
                    url: $request->getUrl(),
                );

                if ($this->successRecognizer->recognize($response)) {
                    $msg->setSuccess();
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

                $this->ctx
                    ->setMessage(new ScraperMessage(
                        payload: $msg,
                        url: $request->getUrl(),
                        isError: true
                    ));
            }
        }
    }
}