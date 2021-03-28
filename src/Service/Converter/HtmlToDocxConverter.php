<?php

declare(strict_types=1);

namespace App\Service\Converter;

use App\Service\Converter\DocumentFiller\FillerFactory;
use App\Service\Converter\DocumentFiller\PhpWordFillerInterface;
use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Component\DomCrawler\Crawler;

class HtmlToDocxConverter implements ConverterInterface
{
    /**
     * @var Crawler
     */
    private $crawler;

    /**
     * @var PhpWord
     */
    private $phpWord;

    /**
     * @var PhpWordFillerInterface
     */
    private $documentFiller;

    /**
     * @var FillerFactory
     */
    private $fillerFactory;

    private $html;

    public function __construct(FillerFactory $fillerFactory)
    {
        $this->fillerFactory = $fillerFactory;
    }

    /**
     * @param string $html
     * @param string $converterType
     *
     * @return ConverterInterface
     *
     * @throws \Exception
     */
    public function init(string $html, string $converterType): ConverterInterface
    {
        $this->html = $this->cleanHtml($html);
        $this->crawler = new Crawler($this->html);
        $this->phpWord = new PhpWord();

        $this->documentFiller = $this->fillerFactory->createPhpWordFiller($converterType);
        $this->documentFiller->init($this->phpWord);

        return $this;
    }

    /**
     * Возвращает путь к сконвертированному файлу.
     *
     * @param string $directory
     * @param string $name
     *
     * @return string
     *
     * @throws Exception
     */
    public function convertToDirWithName(string $directory, string $name): string
    {
        $this->documentFiller->fillDocx($this->crawler);
        $objWriter = IOFactory::createWriter($this->phpWord, 'Word2007');
        $objWriter->save($directory.$name);

        return $directory.$name;
    }

    public function convertToTemp()
    {
        $tmpFile = tmpfile();
        $tempFilePath = stream_get_meta_data($tmpFile)['uri'];
        $this->documentFiller->fillDocx($this->crawler);
        $objWriter = IOFactory::createWriter($this->phpWord, 'Word2007');
        $objWriter->save($tempFilePath);

        return $tmpFile;
    }

    private function cleanHtml(string $html)
    {
        $html = str_replace(["\n", "\r"], '', $html);
        $html = str_replace(['&lt;', '&gt;', '&amp;'], ['_lt_', '_gt_', '_amp_'], $html);
        $html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');
        $html = str_replace('&', '&amp;', $html);
        $html = str_replace(['_lt_', '_gt_', '_amp_'], ['&lt;', '&gt;', '&amp;'], $html);
        $html = str_replace(['<tbody>', '</tbody>'], ['', ''], $html);
        $html = preg_replace('/<style>((.|\n)*?|)<\/style>/', '', $html);

        return $html;
    }
}
