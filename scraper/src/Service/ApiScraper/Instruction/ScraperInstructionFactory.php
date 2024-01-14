<?php

namespace App\Service\ApiScraper\Instruction;

use App\Entity\DataSchema;
use App\Entity\RequestParameter;
use App\Entity\ResponseField;
use App\Message\Scraper\Enum\HttpMethodsEnum;
use App\Message\Scraper\Enum\OutputFormatsEnum;
use App\Message\Scraper\StartScraperCommand;
use App\Repository\DataSchema\DataSchemaRepository;
use App\Repository\OutputSchema\OutputSchemaRepository;
use App\Service\ApiScraper\Instruction\DTO\ParsingConfigData;
use App\Service\ApiScraper\Instruction\DTO\RequestConfigData;
use App\Service\ApiScraper\Instruction\DTO\RequestData;
use App\Service\ApiScraper\Instruction\DTO\RequestParameterData;
use App\Service\ApiScraper\Instruction\DTO\ResponseData;
use App\Service\ApiScraper\Instruction\DTO\ResponseFieldData;
use App\Service\ApiScraper\Instruction\DTO\ScraperSchemaData;
use App\Service\ApiScraper\Instruction\Instruction\ScraperInstruction;
use Doctrine\ORM\NonUniqueResultException;
use RuntimeException;


final readonly class ScraperInstructionFactory
{

    public function __construct(
        private OutputSchemaRepository $outputSchemaRepository,
        private DataSchemaRepository   $dataSchemaRepository,
        private string                 $baseFilePath
    )
    {

    }

    /**
     * Преобразовать схему запроса из базы данных в связанную структуру, которую можно последовательно обойти, отправляя запросы
     * и используя результат предыдущего запроса для последующего
     * @throws NonUniqueResultException
     */
    public function buildInstructionFromCommand(StartScraperCommand $startParsingCommand): ScraperInstruction
    {

        $osRepo = $this->outputSchemaRepository;
        $osSchema = $osRepo->find($startParsingCommand->getSchema());

        if ($osSchema === null) {
            throw new RuntimeException('[ScraperInstructionFactory] DataSchema with id [' . $startParsingCommand->getSchema() . '] not found');
        }

        $highPriorityDataSchema = $this->dataSchemaRepository->findHighPrioritySchemaByGroup($osSchema->getGroupTag());
        return $this->resolveInstruction($highPriorityDataSchema, $startParsingCommand);

    }

    private function resolveInstruction(DataSchema $schema, StartScraperCommand $command): ScraperInstruction
    {

        /**
         *Конфигурация для отправки запроса
         */
        $requestConfig = new RequestConfigData(
            method: HttpMethodsEnum::from($command->getMethod()),
            secret: $command->getSecret(),
            delay: $command->getDelay(),
            authToken: $command->getAuthToken());

        /**
         *Конфигурация парсинга в файл
         */
        $parsingConfig = new ParsingConfigData(
            $command->getFileName(),
            OutputFormatsEnum::from($command->getFormat()),
            $this->baseFilePath
        );
        /**
         *Загрузка базового конфига
         */
        $instruction = new ScraperInstruction(
            $requestConfig,
            $parsingConfig,
            tag: $schema->getGroupTag()
        );

        /**
         *
         * Рекурсивное формирования объекта иснтрукции со связным списком схем связанных запросов
         * @param DataSchema $schema
         * @param ScraperInstruction $instruction
         * @return ScraperSchemaData
         */
        $resolve = static function (DataSchema $schema, ScraperInstruction $instruction) use (&$resolve): ScraperSchemaData {

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

            $parsingSchemaData = new ScraperSchemaData(
                requestData: $requestData,
                responseData: $responseData,
                needsAuth: $schema->isNeedsAuth(),
                fqcn: $schema->getFqcn(),
                executionOrder: $schema->getExecutionOrder()
            );

            $instruction->push($parsingSchemaData);

            return $parsingSchemaData;
        };

        $resolve($schema, $instruction);

        return $instruction;
    }
}