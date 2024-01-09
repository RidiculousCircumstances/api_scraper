<?php

namespace App\Service\ApiScraper\ScraperClient;

use App\Service\ApiScraper\Context\ScraperContext;
use App\Service\ApiScraper\HttpClient\Interface\ClientInterface;
use App\Service\ApiScraper\HttpClient\RequestAdapter;
use App\Service\ApiScraper\HttpClient\RequestPayloadBuilder\RequestPayloadBuilderFactory;
use App\Service\ApiScraper\Instruction\DTO\ScraperInstructionData;
use App\Service\ApiScraper\PayloadPipe\PayloadTransformer\ExternalValueLoader;
use App\Service\ApiScraper\PayloadPipe\PayloadTransformer\PageIncrementor;
use App\Service\ApiScraper\PayloadPipe\PayloadTransformer\PayloadSigner\PayloadSigner;
use App\Service\ApiScraper\PayloadPipe\PayloadTransformer\TimeStamper;
use App\Service\ApiScraper\PayloadPipe\PayloadTransformPipe;
use App\Service\ApiScraper\ResponseRegistry\ResponseRecord;
use App\Service\ApiScraper\ResponseRegistry\ResponseRegistry;
use App\Service\ApiScraper\ScraperClient\Interface\ApiScraperClientInterface;
use App\Service\ApiScraper\ScraperClient\SuccessRecognizer\Interface\SuccessRecognizerInterface;
use Psr\Http\Client\ClientExceptionInterface;

final readonly class ScraperClient implements ApiScraperClientInterface
{

    public function __construct(
        private ScraperContext             $ctx,
        private ScraperInstructionData     $instruction,
        private ClientInterface            $httpClient,
        private SuccessRecognizerInterface $successRecognizer
    )
    {
    }

    public function execInstruction(): void
    {
        $registry = new ResponseRegistry();
        $this->instruction->rewind();

        while (!$this->instruction->executed()) {
            $schema = $this->instruction->extract();
            $requestData = $schema->getRequestData();

            $payload = PayloadTransformPipe::payload($requestData)
                ->add(new TimeStamper())
                ->add(new ExternalValueLoader($registry))
                ->add(new PageIncrementor($this->ctx))
                ->add(new PayloadSigner($this->instruction->getSecret()))
                ->exec();

            $payloadBuilder = RequestPayloadBuilderFactory::getBuilder($this->instruction->getMethod());
            $request = RequestAdapter::from($schema)
                ->setBody($payloadBuilder->build($payload))
                ->setDelay($this->instruction->getDelay());

            try {
                $response = $this->httpClient->requestSource($request);

                $msg = new ScraperMessage(
                    payload: $response,
                    url: $request->getUrl(),
                );

                if ($this->successRecognizer->recognize($response)) {
                    $msg->setSuccess();
                }
                $this->successRecognizer->setPrevious($response);

                $this->ctx->setMessage($msg);

                $registry->add(new ResponseRecord(
                    requestId: $schema->getFqcn(),
                    content: $response
                ));
            } catch (ClientExceptionInterface $exception) {
                $this->ctx
                    ->setMessage(new ScraperMessage(
                        payload: $exception,
                        url: $request->getUrl(),
                        isError: true
                    ));
            }

        }
    }
}