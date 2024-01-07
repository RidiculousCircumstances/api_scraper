<?php

namespace App\Service\Parsing;

use App\Entity\DataSchema;
use App\Message\Parsing\StartParsingCommand;
use App\Repository\DataSchema\DataSchemaRepository;
use App\Service\Parsing\DTO\ParsingInstructionData;
use App\Service\Parsing\DTO\ParsingInstructionStackData;
use App\Service\Parsing\DTO\RequestData;
use App\Service\Parsing\DTO\RequestParameterData;
use App\Service\Parsing\DTO\ResponseData;
use App\Service\Parsing\DTO\ResponseFieldData;
use RuntimeException;

/**
 * Резолвит схему запроса в связанную структуру, которую можно последовательно обойти, отправляя запросы
 * и используя результат предыдущего запроса для последующего
 */
final readonly class PrepareParsingInstructionService
{
    public function __construct(private DataSchemaRepository $dataSchemaRepository)
    {

    }

    private function resolveInstruction(DataSchema $schema): ParsingInstructionStackData
    {

        $stack = new ParsingInstructionStackData();

        $resolve = static function(DataSchema $schema) use (&$order, &$resolve, $stack): ParsingInstructionData {
            $order ++;
            $requestParameters = $schema->getRequestParameters();

            $requestParameterDataArray = [];
            foreach($requestParameters as $parameter) {

                $externalSchema = $parameter->getExternalSchema();

                $requestParameterData = new RequestParameterData(
                    key: $parameter->getKey(),value: $parameter->getValue()
                );

                if($externalSchema !== null) {
                    $externalSchemaData = $resolve($externalSchema);
                    $requestParameterData->setExternalSource($externalSchemaData);
                }

                $requestParameterDataArray[] = $requestParameterData;

            }

            $requestData = new RequestData(
                targetUrl: $schema->getUrl(),
                httpMethod: 'post',
                requestParameters: $requestParameterDataArray
            );

            $responseFields = $schema->getResponseFields();

            $responseFieldDataArray = [];

            foreach($responseFields as $field) {
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

        $resolve($schema);

        return $stack;
    }

    public function prepareParsingInstruction(StartParsingCommand $startParsingCommand): ParsingInstructionStackData
    {

        $repo = $this->dataSchemaRepository;

        $schema = $repo->find($startParsingCommand->getSchema());

        if($schema === null) {
            throw new RuntimeException('[PrepareParsingInstructionService] DataSchema with id [' . $startParsingCommand->getSchema() . '] not found');
        }

        return $this->resolveInstruction($schema);

    }
}