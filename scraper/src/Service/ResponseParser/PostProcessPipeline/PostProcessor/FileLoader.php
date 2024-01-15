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
class FileLoader implements PostProcessHandlerInterface
{

    private static string $imageHeaderPattern = '/{{:file=(?<id_column>[\S\s]+)}}/';

    private Client $client;

    public function __construct(private string $outputDirectory)
    {
        $this->client = new Client();
    }


    /**
     * @throws GuzzleException
     */
    public function transform(WritableRowData $writableRow): void
    {
        $headers = $writableRow->getHeaders();

        foreach ($headers as $header) {
            $imageHeaderMatches = [];
            if (!preg_match(self::$imageHeaderPattern, $header, $imageHeaderMatches)) {
                continue;
            }

            $imageUrls = $writableRow->getColumn($header);
            $subDirectoryHeaderName = $imageHeaderMatches['id_column'];


            for ($i = 0, $iMax = count($imageUrls); $i < $iMax; $i++) {
                $url = $imageUrls[$i];

                $directoryNameId = $writableRow->getColumn($subDirectoryHeaderName)[$i];

                if (!file_exists($this->outputDirectory) && !mkdir($this->outputDirectory) && !is_dir($this->outputDirectory)) {
                    throw new \RuntimeException(sprintf('Не удалось создать директорию "%s" для сохранения изображений', $this->outputDirectory));
                }

                $savePath = $this->outputDirectory . $directoryNameId;

                if (!file_exists($savePath) && !mkdir($savePath) && !is_dir($savePath)) {
                    throw new \RuntimeException(sprintf('Не удалось создать директорию "%s" для сохранения изображений', $this->outputDirectory));
                }


                $filename = $savePath . '/' . time();

                $resource = fopen($filename, 'wb');

                $this->client->request('get', $url, ['sink' => $resource, 'delay' => 500]);

                $ext = mime2ext(mime_content_type($filename));

                rename($filename, $filename . '.' . $ext);

            }

            /**
             * Удаляем техническую колонку. Она не должна попасть в конечный файл.
             */
            $writableRow->deleteColumn($header);
        }
    }

}