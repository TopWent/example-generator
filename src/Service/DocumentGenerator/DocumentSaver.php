<?php

declare(strict_types=1);

namespace App\Service\DocumentGenerator;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Transliterator;

class DocumentSaver implements DocumentSaverInterface
{
    /**
     * @param $tmpFile
     * @param string $filename
     *
     * @return string
     *
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function save($tmpFile, string $filename): string
    {
        $httpClient = HttpClient::create([
            'verify_peer' => 'false' !== $_ENV['VERIFY_SSL'],
            'verify_host' => 'false' !== $_ENV['VERIFY_SSL'],
        ]);

        // готовим body, переводя имя файла в транслит
        $filename = $this->transliterateFilename($filename);
        $dataPart = DataPart::fromPath(stream_get_meta_data($tmpFile)['uri'], $filename);
        $formData = new FormDataPart(['file' => $dataPart]);

        $response = $httpClient->request('POST', $_ENV['FILESTORAGE_URL'], [
            'headers' => $formData->getPreparedHeaders()->toArray(),
            'body' => $formData->bodyToIterable(),
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new \Exception('Проблемы с загрузкой документа в файлохранилище. Код: '.$response->getStatusCode());
        }

        return $response->toArray()['fid'];
    }

    /**
     * @param string $fileName
     * @return string
     */
    private function transliterateFilename(string $fileName): string
    {
        $transliterator = Transliterator::create('Any-Latin');
        $transliteratorToASCII = Transliterator::create('Latin-ASCII');

        return $transliteratorToASCII->transliterate(
            $transliterator->transliterate($fileName)
        );
    }
}
