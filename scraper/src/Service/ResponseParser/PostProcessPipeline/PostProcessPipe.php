<?php

namespace App\Service\ResponseParser\PostProcessPipeline;

use App\Service\ResponseParser\PostProcessPipeline\Interface\PostProcessHandlerInterface;
use App\Service\ResponseParser\ResponseMapper\DTO\WritableRowData;
use Ds\Queue;

class PostProcessPipe
{

    /**
     * @var Queue<PostProcessHandlerInterface> $handlersQueue
     */
    private Queue $handlersQueue;

    private WritableRowData $writableRow;


    public function __construct()
    {
        $this->handlersQueue = new Queue();
    }

    public static function payload(WritableRowData $writableRow): static
    {
        $static = new static();
        $static->writableRow = $writableRow;
        return $static;
    }

    public function with(PostProcessHandlerInterface $handler): static
    {
        $this->handlersQueue->push($handler);
        return $this;
    }

    public function transform(): WritableRowData
    {
        while (!$this->handlersQueue->isEmpty()) {
            $handler = $this->handlersQueue->pop();
            $handler->transform($this->writableRow);
        }

        return $this->writableRow;
    }
}