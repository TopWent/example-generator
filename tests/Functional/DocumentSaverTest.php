<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Service\DocumentGenerator\DocumentSaver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;

/**
 * Тестирование сохранения сгенерированных файлов в файлохранилище.
 */
class DocumentSaverTest extends TestCase
{
    public function testSavingWithCorrectName()
    {
        $documentSaver = new DocumentSaver();
        $tmpFile = tmpfile();
        $aimFileName = 'Имя файла на русском.docx';

        $fid = $documentSaver->save($tmpFile, $aimFileName);

        $client = HttpClient::create();
        $url = $_ENV['FILESTORAGE_URL'].'/info/'.$fid;
        $response = $client->request('GET', $url);

        $this->assertEquals(200, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('fileName', $content);
        $this->assertEquals('Ima fajla na russkom.docx', $content['fileName']);
    }
}
