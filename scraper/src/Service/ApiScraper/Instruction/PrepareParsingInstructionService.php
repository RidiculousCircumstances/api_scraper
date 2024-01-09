<?php

namespace App\Service\ApiScraper\Instruction;

use App\Entity\DataSchema;
use App\Entity\RequestParameter;
use App\Entity\ResponseField;
use App\Message\Parsing\Enum\HttpMethodsEnum;
use App\Message\Parsing\StartParsingCommand;
use App\Repository\DataSchema\DataSchemaRepository;
use App\Service\ApiScraper\Instruction\DTO\ParsingSchemaData;
use App\Service\ApiScraper\Instruction\DTO\RequestData;
use App\Service\ApiScraper\Instruction\DTO\RequestParameterData;
use App\Service\ApiScraper\Instruction\DTO\ResponseData;
use App\Service\ApiScraper\Instruction\DTO\ResponseFieldData;
use App\Service\ApiScraper\Instruction\DTO\ScraperInstructionData;
use RuntimeException;


final readonly class PrepareParsingInstructionService
{
    public function __construct(private DataSchemaRepository $dataSchemaRepository)
    {

    }

    /**
     * Преобразовать схему запроса в связанную структуру, которую можно последовательно обойти, отправляя запросы
     * и используя результат предыдущего запроса для последующего
     */
    public function prepareParsingInstruction(StartParsingCommand $startParsingCommand): ScraperInstructionData
    {

        $repo = $this->dataSchemaRepository;

        $schema = $repo->find($startParsingCommand->getSchema());

        if ($schema === null) {
            throw new RuntimeException('[PrepareParsingInstructionService] DataSchema with id [' . $startParsingCommand->getSchema() . '] not found');
        }

        return $this->resolveInstruction($schema, $startParsingCommand);

    }

    private function resolveInstruction(DataSchema $schema, StartParsingCommand $command): ScraperInstructionData
    {

        $instruction = new ScraperInstructionData(
            method: HttpMethodsEnum::from($command->getMethod()),
            secret: $command->getSecret(),
            delay: $command->getDelay()
        );

        $resolve = static function (DataSchema $schema, ScraperInstructionData $instruction) use (&$resolve): ParsingSchemaData {

            $requestParameters = $schema->getRequestParameters();

            $requestParameterDataArray = $requestParameters->map(function (RequestParameter $parameter) use ($resolve, $instruction) {
                $externalSchema = $parameter->getExternalSchema();

                $requestParameterData = new RequestParameterData(
                    key: $parameter->getKey(), value: $parameter->getValue()
                );

                if ($externalSchema !== null) {
                    $externalSchemaData = $resolve($externalSchema, $instruction);
                    $requestParameterData->setExternalSourceId($externalSchemaData->getFqcn());
                }

                return $requestParameterData;
            });

            $requestData = new RequestData(
                targetUrl: $schema->getUrl(),
                httpMethod: HttpMethodsEnum::GET,
                requestParameters: $requestParameterDataArray->toArray()
            );

            $responseFields = $schema->getResponseFields();

            $responseFieldDataArray = $responseFields->map(function (ResponseField $responseField) {
                return new ResponseFieldData(
                    responsePath: $responseField->getDataPath(),
                    outputName: $responseField->getOutputName()
                );
            });

            $responseData = new ResponseData($responseFieldDataArray->toArray());

            $parsingSchemaData = new ParsingSchemaData(
                requestData: $requestData,
                responseData: $responseData
            );

            $instruction->push($parsingSchemaData);

            return $parsingSchemaData;
        };

        $resolve($schema, $instruction);

        return $instruction;
    }
}