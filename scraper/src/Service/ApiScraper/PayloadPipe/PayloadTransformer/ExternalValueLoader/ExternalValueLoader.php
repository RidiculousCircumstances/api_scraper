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

    private Queue $queue;

    private int $pausedIndex = 0;


    public function __construct(private readonly ResponseRegistry $registry, private readonly SuspendableInterface $instruction)
    {
        $this->traverser = new StringPathTraverser();
        $this->queue = new Queue();
    }

    public function transform(array $parameters, array &$payload, bool $defer = false): void
    {

        if ($defer) {
            return;
        }

        if ($this->queue->isEmpty()) {

            $count = count($parameters);

            for ($i = $this->pausedIndex; $i < $count; $i++) {

                $parameter = $parameters[$i];

                $externalSourceId = $parameter->getExternalSourceId();

                /**
                 * Если поле не имеет внешнего источника - пропускаем
                 */
                if (!$externalSourceId) {
                    $payload[$parameter->getKey()] = 1;
                    continue;
                }

                $externalPath = $parameter->getValue();
                $externalData = $this->registry->get($externalSourceId);

                /**
                 * Если путь не ссылается на множество айтемов - получаем значение обычным образом
                 */
                if (!$this->traverser->checkMultipleItemsInPath($externalPath)) {
                    $payload[$parameter->getKey()] = m($externalData)(get_by_dot_keys($externalPath))();
                    continue;
                }

                /**
                 * Записыавем индекс, с которого продолжим
                 */
                if ($count >= $i + 1) {
                    $this->pausedIndex = $i + 1;
                }

                /**
                 * Иначе блокируем очередь схем на текущей.
                 * Нам нужно выполнить запрос для каждого айтема во внешнем источнике.
                 *
                 * Таким образом, скрапер клиент продолжит выполнять запрос
                 */
                $valueGenerator = function () use ($externalPath, $externalData, &$payload, $parameter, $parameters) {
                    if (!$this->instruction->isSuspended()) {
                        $this->instruction->suspended(true);
                    }

                    $value = $this->handleItems($externalPath, $externalData->getContent());

                    $payload[$parameter->getKey()] = $value;

                    if ($this->queue->isEmpty()) {
                        $this->instruction->suspended(false);
                    }

                    $this->transform($parameters, $payload, true);

                };

                $this->valueGenerator = $valueGenerator;
                $valueGenerator();
                break;
            }
        } else {
            $valueGenerator = $this->valueGenerator;
            $valueGenerator();
        }

    }

    private function handleItems(string $path, array $content): mixed
    {

        $items = $this->traverser->extractItems($path, $content);
        if ($this->queue->isEmpty()) {
            foreach ($items as $item) {
                $this->queue->push($item);
            }
        }
        $item = $this->queue->pop();
        return $this->traverser->extractValue($path, $item);

    }

}