<?php

namespace App\Service\ResponseParser\PostProcessPipeline\PostProcessor;

use App\Service\ResponseParser\PostProcessPipeline\Interface\PostProcessHandlerInterface;
use App\Service\ResponseParser\ResponseMapper\DTO\WritableRowData;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Загружает изображения, если встречает командный хедер. id_column используется для
 * директории, в которую будет сохранено изображение
 */
class ImageLoader implements PostProcessHandlerInterface
{

    private static string $imageHeaderPattern = '/{{:image=(?<id_column>\S+)}}/';

    private Client $client;

    public function __construct(private string $outputDirectory) {
        $this->client = new Client();
    }


    /**
     * @throws GuzzleException
     */
    public function transform(WritableRowData $writableRow): void
    {
        $headers = $writableRow->getHeaders();

        foreach($headers as $header) {
            $imageHeaderMatches = [];
            if(!preg_match(self::$imageHeaderPattern, $header, $imageHeaderMatches)) {
                continue;
            }

            $imageUrls = $writableRow->getColumn($header);
            $subDirectoryName = $imageHeaderMatches['id_column'];
            $savePath = $this->outputDirectory . $subDirectoryName;

            if(!file_exists($savePath) && !mkdir($savePath) && !is_dir($savePath)) {
                throw new \RuntimeException(sprintf('Не удалось создать директорию "%s" для сохранения изображений', $savePath));
            }

            foreach($imageUrls as $url) {
                $resource = fopen($savePath, 'wb');
                $this->client->request('get', $url, ['sink' => $resource, 'delay' => 500]);
            }

            /**
             * Удаляем техническую колонку. Она не должна попасть в конечный файл.
             */
            $writableRow->deleteColumn($header);
        }
    }

}