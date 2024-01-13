<?php

namespace App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\ExternalValueLoader;


use App\Service\ApiScraper\Instruction\DTO\RequestData;
use App\Service\ApiScraper\Instruction\DTO\RequestParameterData;
use App\Service\ApiScraper\PayloadPipeline\Interface\PayloadTransformerInterface;
use App\Service\ApiScraper\PayloadPipeline\PayloadTransformer\Interface\SuspendableInterface;
use App\Service\ApiScraper\ResponseRegistry\ResponseRegistry;
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
final class ExternalValueLoader implements PayloadTransformerInterface
{

    private static self|null $instance = null;
    private StringPathExplorer $pathExplorer;
    private Queue $externalItemsQueue;
    /**
     * @var Queue<RequestParameterData> $parametersQueue
     */
    private Queue $parametersQueue;
    private $currentExternalItem = null;

    private function __construct(private readonly ResponseRegistry $registry, private readonly SuspendableInterface $instruction)
    {
        $this->pathExplorer = new StringPathExplorer();
        $this->externalItemsQueue = new Queue();
        $this->parametersQueue = new Queue();
    }

    /**
     * TODO: проверить, сохраняет ли контекст
     * @param ResponseRegistry $registry
     * @param SuspendableInterface $instruction
     * @return self|null
     */
    public static function new(ResponseRegistry $registry, SuspendableInterface $instruction): self|null
    {
        if (self::$instance === null || !$instruction->isSuspended()) {
            self::$instance = new self($registry, $instruction);
        }

        return self::$instance;
    }

    public static function getFresh(ResponseRegistry $registry, SuspendableInterface $instruction): ExternalValueLoader
    {
        return new self($registry, $instruction);
    }

    /**
     * TODO: в текущей имплементации не будет сохранять контекст между после оттправки сформированного запроса.
     * Он должен быть синглтоном в период саспенда
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
            $externalData = $this->registry->get($externalSourceId);

            /**
             * Если путь не ссылается на множество айтемов - получаем значение обычным образом
             */
            if (!$this->pathExplorer->checkMultipleItemsInPath($externalPath)) {
                $payloadRef[$parameter->getKey()] = m($externalData)(get_by_dot_keys($externalPath))();
                continue;
            }

            /**
             * Иначе блокируем очередь схем на текущей.
             * Нам нужно выполнить запрос для каждого айтема во внешнем источнике.
             *
             * Таким образом, скрапер клиент продолжит выполнять запрос
             */

            if (!$this->instruction->isSuspended()) {
                $this->instruction->suspended(true);
            }

            $value = $this->handleItems($externalPath, $externalData->getContent());

            $payloadRef[$parameter->getKey()] = $value;

            if ($this->externalItemsQueue->isEmpty()) {
                $this->instruction->suspended(false);
            }

        }

    }

    private function handleItems(string $path, array $content): mixed
    {

        $items = $this->pathExplorer->extractItems($path, $content);
        if ($this->externalItemsQueue->isEmpty()) {
            $this->externalItemsQueue->push(...$items);
        }

        $item = $this->externalItemsQueue->pop();

        return $this->pathExplorer->extractValue($path, $item);

    }

}