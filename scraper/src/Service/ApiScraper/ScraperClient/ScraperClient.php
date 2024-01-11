<?php

namespace App\Service\ApiScraper\ScraperClient;

use App\Service\ApiScraper\Context\ScraperContext;
use App\Service\ApiScraper\HttpClient\Interface\ClientInterface;
use App\Service\ApiScraper\HttpClient\RequestAdapter;
use App\Service\ApiScraper\HttpClient\RequestPayloadBuilder\RequestPayloadBuilderFactory;
use App\Service\ApiScraper\Instruction\DTO\ScraperInstructionData;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\ExternalValueLoader\ExternalValueLoader;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\PageIncrementor;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\PayloadSigner\PayloadSigner;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\TimeStamper;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\UrlSegmentReplacer\UrlSegmentReplacer;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformPipe;
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
        $instruction = $this->instruction;

        $instruction->rewind();

        while (!$instruction->executed()) {

            usleep($instruction->getDelay());

            $schema = $instruction->extract();
            $requestData = $schema->getRequestData();

            PayloadTransformPipe::payload($requestData)
                ->with(new TimeStamper())
                ->with(ExternalValueLoader::new($registry, $instruction))
                ->with(new UrlSegmentReplacer())
                ->with(new PageIncrementor($this->ctx))
                ->with(new PayloadSigner($instruction->getSecret()))
                ->transform();

            $payloadBuilder = RequestPayloadBuilderFactory::getBuilder($instruction->getMethod());
            $request = RequestAdapter::schema($schema)
                ->setBody($payloadBuilder->build($requestData->getCrudePayload()))
                ->setHeaders([
                    'X-AUTH_TOKEN' => $instruction->getAuthToken()
                ]);

            try {
                $response = $this->httpClient->request($request);
                $msg = new ScraperMessage(
                    payload: $response,
                    url: $request->getUrl(),
                );
                if ($this->successRecognizer->recognize($response)) {
                    $msg->setSuccess();
                }
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