<?php

namespace App\Service\Parsing\Instruction;

use App\Entity\DataSchema;
use App\Message\Parsing\StartParsingCommand;
use App\Repository\DataSchema\DataSchemaRepository;
use App\Service\Parsing\Instruction\DTO\ParsingInstructionData;
use App\Service\Parsing\Instruction\DTO\ParsingInstructionStackData;
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
    public function prepareParsingInstruction(StartParsingCommand $startParsingCommand): ParsingInstructionStackData
    {

        $repo = $this->dataSchemaRepository;

        $schema = $repo->find($startParsingCommand->getSchema());

        if ($schema === null) {
            throw new RuntimeException('[PrepareParsingInstructionService] DataSchema with id [' . $startParsingCommand->getSchema() . '] not found');
        }

        return $this->resolveInstruction($schema);

    }

    private function resolveInstruction(DataSchema $schema): ParsingInstructionStackData
    {

        $stack = new ParsingInstructionStackData();

        $resolve = static function (DataSchema $schema, ParsingInstructionStackData $stack) use (&$order, &$resolve): ParsingInstructionData {
            $order++;
            $requestParameters = $schema->getRequestParameters();

            $requestParameterDataArray = [];
            foreach ($requestParameters as $parameter) {

                $externalSchema = $parameter->getExternalSchema();

                $requestParameterData = new RequestParameterData(
                    key: $parameter->getKey(), value: $parameter->getValue()
                );

                if ($externalSchema !== null) {
                    $externalSchemaData = $resolve($externalSchema);
                    $requestParameterData->setExternalSourceId($externalSchemaData->getFqcn());
                }

                $requestParameterDataArray[] = $requestParameterData;

            }

            $requestData = new RequestData(
                targetUrl: $schema->getUrl(),
                httpMethod: 'get',
                requestParameters: $requestParameterDataArray
            );

            $responseFields = $schema->getResponseFields();

            $responseFieldDataArray = [];

            foreach ($responseFields as $field) {
                $responseFieldDataArray[] = new ResponseFieldData(
                    responsePath: $field->getDataPath(),
                    outputName: $field->getOutputName()
                );
            }

            $responseData = new ResponseData($responseFieldDataArray);

            $parsingInstructionData = new ParsingInstructionData(
                requestData: $requestData,
                responseData: $responseData
            );

            $stack->put($parsingInstructionData);

            return $parsingInstructionData;
        };

        $resolve($schema, $stack);

        return $stack;
    }
}