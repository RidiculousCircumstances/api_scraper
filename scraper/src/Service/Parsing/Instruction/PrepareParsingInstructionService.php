<?php

namespace App\Service\Parsing\Instruction;

use App\Entity\DataSchema;
use App\Entity\RequestParameter;
use App\Entity\ResponseField;
use App\Message\Parsing\Enum\HttpMethodsEnum;
use App\Message\Parsing\StartParsingCommand;
use App\Repository\DataSchema\DataSchemaRepository;
use App\Service\Parsing\Instruction\DTO\ParsingInstructionData;
use App\Service\Parsing\Instruction\DTO\ParsingInstructionQueueData;
use App\Service\Parsing\Instruction\DTO\RequestData;
use App\Service\Parsing\Instruction\DTO\RequestParameterData;
use App\Service\Parsing\Instruction\DTO\ResponseData;
use App\Service\Parsing\Instruction\DTO\ResponseFieldData;
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
    public function prepareParsingInstruction(StartParsingCommand $startParsingCommand): ParsingInstructionQueueData
    {

        $repo = $this->dataSchemaRepository;

        $schema = $repo->find($startParsingCommand->getSchema());

        if ($schema === null) {
            throw new RuntimeException('[PrepareParsingInstructionService] DataSchema with id [' . $startParsingCommand->getSchema() . '] not found');
        }

        return $this->resolveInstruction($schema, $startParsingCommand);

    }

    private function resolveInstruction(DataSchema $schema, StartParsingCommand $command): ParsingInstructionQueueData
    {

        $queue = new ParsingInstructionQueueData();

        $resolve = static function (DataSchema $schema, ParsingInstructionQueueData $queue) use (&$resolve, $command): ParsingInstructionData {

            $requestParameters = $schema->getRequestParameters();

            $requestParameterDataArray = $requestParameters->map(function (RequestParameter $parameter) use ($resolve, $queue) {
                $externalSchema = $parameter->getExternalSchema();

                $requestParameterData = new RequestParameterData(
                    key: $parameter->getKey(), value: $parameter->getValue()
                );

                if ($externalSchema !== null) {
                    $externalSchemaData = $resolve($externalSchema, $queue);
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

            $parsingInstructionData = new ParsingInstructionData(
                requestData: $requestData,
                responseData: $responseData,
                secret: $command->getSecret(),
                method: HttpMethodsEnum::from($command->getMethod())
            );

            $queue->put($parsingInstructionData);

            return $parsingInstructionData;
        };

        $resolve($schema, $queue);

        return $queue;
    }
}