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

    private StringPathExplorer $pathExplorer;

    private \Closure $valueGenerator;

    private Queue $externalItemsQueue;

    /**
     * @var Queue<RequestParameterData> $parametersQueue
     */
    private Queue $parametersQueue;

    public function __construct(private readonly ResponseRegistry $registry, private readonly SuspendableInterface $instruction)
    {
        $this->pathExplorer = new StringPathExplorer();
        $this->externalItemsQueue = new Queue();
        $this->parametersQueue = new Queue();
    }

    public function transform(RequestData $requestData): void
    {
        if (!$this->externalItemsQueue->isEmpty()) {
            $valueGenerator = $this->valueGenerator;
            $valueGenerator();
            return;
        }

        $parameters = $requestData->getRequestParameters();
        $payload = &$requestData->getCrudePayloadReference();

        /***
         * {{:url_parameter}}
         *
         * чтобы воспользоваться подстановкой в урл, нужно написать в силе api.drom.ru/v1.3/bulls/{{:url_parameter=id}}/user
         *
         * в теле запроса нужно создать поле с ключом {{:url_parameter=some_id}}
         * в значение нужно записать путь ко внешнему запросу,
         * в ссылке указать схему запроса
         *
         * что надо сделать:
         * 1) разбить урл по /
         * 2) выбрать все параметры
         * 3) положить в колбэк формироование урл с подстановкой
         * 4) извлечь значения, вызвать колбэк
         *
         * стоп
         * если у нас параметры загрузятся обычным флоу,
         * то работа реплейсера сводится к перебору полезной нагрузки, извлечением значения и удалением параметра
         * whoa!
         */

        if ($this->parametersQueue->isEmpty()) {
            $this->parametersQueue->push(...$parameters);
        }


        $parameter = $this->parametersQueue->pop();
        $externalSourceId = $parameter->getExternalSourceId();

        $loopLock = false;
        while ($externalSourceId === null) {
            $loopLock = true;

            if ($this->parametersQueue->count() === 0) {
                return;
            }

            $parameter = $this->parametersQueue->pop();

            $externalSourceId = $parameter->getExternalSourceId();
        }

        if ($loopLock) {
            return;
        }

        $externalPath = $parameter->getValue();
        $externalData = $this->registry->get($externalSourceId);

        /**
         * Если путь не ссылается на множество айтемов - получаем значение обычным образом
         */
        if (!$this->pathExplorer->checkMultipleItemsInPath($externalPath)) {
            $payload[$parameter->getKey()] = m($externalData)(get_by_dot_keys($externalPath))();
            return;
        }

        /**
         * Иначе блокируем очередь схем на текущей.
         * Нам нужно выполнить запрос для каждого айтема во внешнем источнике.
         *
         * Таким образом, скрапер клиент продолжит выполнять запрос
         */
        $valueGenerator = function () use ($externalPath, $externalData, &$payload, $parameter) {
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

        $items = $this->pathExplorer->extractItems($path, $content);
        if ($this->externalItemsQueue->isEmpty()) {
            $this->externalItemsQueue->push(...$items);
        }
        $item = $this->externalItemsQueue->pop();
        return $this->pathExplorer->extractValue($path, $item);

    }

}