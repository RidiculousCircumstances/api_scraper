<?php

namespace App\Service\ApiScraper\PayloadPipe\PayloadTransformer\ExternalValueLoader;


use App\Service\ApiScraper\PayloadPipe\Interface\PayloadTransformerInterface;
use App\Service\ApiScraper\PayloadPipe\PayloadTransformer\Interface\SuspendableInterface;
use App\Service\ApiScraper\ResponseRegistry\ResponseRegistry;
use App\Service\ApiScraper\StringPathTraverser\StringPathTraverser;
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
class ExternalValueLoader implements PayloadTransformerInterface
{

    private StringPathTraverser $traverser;

    private $valueGenerator;

    private Queue $externalItemsQueue;

    private Queue $parametersQueue;

    public function __construct(private readonly ResponseRegistry $registry, private readonly SuspendableInterface $instruction)
    {
        $this->traverser = new StringPathTraverser();
        $this->externalItemsQueue = new Queue();
        $this->parametersQueue = new Queue();
    }

    public function transform(array $parameters, array &$payload): void
    {

        if(!$this->externalItemsQueue->isEmpty()) {
            $valueGenerator = $this->valueGenerator;
            $valueGenerator();
            return;
        }

        if($this->parametersQueue->isEmpty()) {
            $this->parametersQueue->push(...$parameters);
        }

        $parameter = $this->parametersQueue->pop();
        $externalSourceId = $parameter->getExternalSourceId();

        $loopLock = false;
        while ($externalSourceId === null) {
            $loopLock = true;
            $payload[$parameter->getKey()] = 1;

            if($this->parametersQueue->count() === 0) {
                return;
            }

            $parameter = $this->parametersQueue->pop();

            $externalSourceId = $parameter->getExternalSourceId();
        }

        if($loopLock) {
            return;
        }

        $externalPath = $parameter->getValue();
        $externalData = $this->registry->get($externalSourceId);

        /**
         * Если путь не ссылается на множество айтемов - получаем значение обычным образом
         */
        if (!$this->traverser->checkMultipleItemsInPath($externalPath)) {
            $payload[$parameter->getKey()] = m($externalData)(get_by_dot_keys($externalPath))();
            return;
        }

        /**
         * Иначе блокируем очередь схем на текущей.
         * Нам нужно выполнить запрос для каждого айтема во внешнем источнике.
         *
         * Таким образом, скрапер клиент продолжит выполнять запрос
         */
        $valueGenerator = function() use ($externalPath, $externalData, &$payload, $parameter) {
            if (!$this->instruction->isSuspended()) {
                $this->instruction->suspended(true);
            }

            $value = $this->handleItems($externalPath, $externalData->getContent());

            $payload[$parameter->getKey()] = $value;

            if ($this->externalItemsQueue->isEmpty()) {
                $this->instruction->suspended(false);
            }
        };

        $this->valueGenerator = $valueGenerator;
        $valueGenerator();

    }

    private function handleItems(string $path, array $content): mixed
    {

        $items = $this->traverser->extractItems($path, $content);
        if ($this->externalItemsQueue->isEmpty()) {
            $this->externalItemsQueue->push(...$items);
        }
        $item = $this->externalItemsQueue->pop();
        return $this->traverser->extractValue($path, $item);

    }

}