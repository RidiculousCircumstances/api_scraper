<?php

namespace App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\ExternalValueLoader;


use App\Service\ApiScraper\Instruction\DTO\RequestData;
use App\Service\ApiScraper\Instruction\DTO\RequestParameterData;
use App\Service\ApiScraper\PayloadPipeline\Interface\PipeHandlerInterface;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\Interface\SuspendableInterface;
use App\Service\ApiScraper\ResponseBag\ResponseBag;
use App\Service\StringPathExplorer\StringPathExplorer;
use Ds\Queue;

/**
 * В чем идея: мы можем связывать запросы. Например, нужно получить массив объявлений,
 * и для каждого - детальную страницу.
 *
 * Формируется цепочка запросов: 1) - запрашиваем список айтемов.
 * Затем для каждого айтема выполняется связанный запрос.
 *
 * ExternalValueLoader выполняет подмену значений для поля в производном запросе
 * значением из базового запроса.
 *
 * То есть, в трансформере имитируется генератор, который итерируется по массиву айтемов,
 * если такой есть. Если нет - происходит обычный вызов
 */
final class ExternalValueLoader implements PipeHandlerInterface
{

    private static bool $hasEmptyResult = false;

    private static self|null $instance = null;
    private StringPathExplorer $pathExplorer;
    private Queue $externalItemsQueue;

    private array|null $currentExternalItem = null;

    /**
     * @var Queue<RequestParameterData> $parametersQueue
     */
    private Queue $parametersQueue;

    private function __construct(private readonly ResponseBag $registry, private readonly SuspendableInterface $instruction)
    {
        $this->pathExplorer = new StringPathExplorer();
        $this->externalItemsQueue = new Queue();
        $this->parametersQueue = new Queue();
    }

    /**
     * Возвращает синглтон, когда очередь выполнения заболкирована.
     * Это нужно для сохранения контекста.
     * @param ResponseBag $registry
     * @param SuspendableInterface $instruction
     * @return self|null
     */
    public static function new(ResponseBag $registry, SuspendableInterface $instruction): self|null
    {
        if (self::$instance === null || !$instruction->isSuspended()) {
            self::$instance = new self($registry, $instruction);
        }

        return self::$instance;
    }

    public static function getFresh(ResponseBag $registry, SuspendableInterface $instruction): ExternalValueLoader
    {
        return new self($registry, $instruction);
    }

    public static function mainRequestReturnedEmptyData(): bool
    {
        return self::$hasEmptyResult;
    }

    /**
     * Подгружает значения из выполненных запросов
     * @param RequestData $requestData
     * @return void
     */
    public function transform(RequestData $requestData): void
    {

        $parameters = $requestData->getRequestParameters();
        $payloadRef = &$requestData->getCrudePayloadReference();

        if ($this->parametersQueue->isEmpty()) {
            $this->parametersQueue->push(...$parameters);
        }

        while (!$this->parametersQueue->isEmpty()) {
            $parameter = $this->parametersQueue->pop();
            $externalSourceId = $parameter->getExternalSourceId();

            if ($externalSourceId === null) {
                continue;
            }

            $externalPath = $parameter->getValue();
            $externalData = $this->registry->getResponseRecordByRequestId($externalSourceId);

            /**
             * Если путь не ссылается на множество айтемов - получаем значение обычным образом
             */
            if (!$this->pathExplorer->checkMultipleItemsInPath($externalPath)) {
                $payloadRef[$parameter->getKey()] = m($externalData)(get_by_dot_keys($externalPath))();
                continue;
            }

            $items = $this->pathExplorer->extractItems($externalPath, $externalData->getContent());

            if ($items === null || count($items) === 0) {
                self::$hasEmptyResult = true;
                break;
            }

            /**
             * Иначе блокируем очередь схем на текущей.
             * Нам нужно выполнить запрос для каждого айтема во внешнем источнике.
             *
             * Таким образом, скрапер клиент продолжит выполнять запрос
             */
            if (!$this->instruction->isSuspended()) {
                $this->instruction->setSuspended(true);
            }

            $value = $this->handleItems($externalPath, $items);

            $payloadRef[$parameter->getKey()] = $value;

            if ($this->externalItemsQueue->isEmpty()) {
                $this->instruction->setSuspended(false);
            }

            if ($this->parametersQueue->isEmpty()) {
                $this->currentExternalItem = null;
            }

        }

    }

    /**
     * Извлекает данные из выполненного запроса.
     * @param string $path
     * @param array $items
     * @return mixed
     */
    private function handleItems(string $path, array $items): mixed
    {

        if ($this->externalItemsQueue->isEmpty()) {
            $this->externalItemsQueue->push(...$items);
        }

        /**
         * Если в рамках одной схемы требуется несколько подстановок - для каждого параметра используем
         * один и тот же айтем, курсор двигать в таком случае не нужно.
         */

        if (!$this->currentExternalItem) {
            $this->currentExternalItem = $this->externalItemsQueue->pop();
        }

        return $this->pathExplorer->extractValue($path, $this->currentExternalItem);

    }

}