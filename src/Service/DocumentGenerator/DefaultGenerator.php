<?php

declare(strict_types=1);

namespace App\Service\DocumentGenerator;

use App\Entity\Template;
use App\Helper\ConverterConfigHandler;
use App\Repository\SpecialTemplateRepository;
use App\Service\Converter\HtmlToDocxConverter;
use App\Service\DocumentGenerator\Replacer\Replacer;
use Exception;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use PhpOffice\PhpWord\TemplateProcessor;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class DefaultGenerator implements GeneratorInterface
{
    /**
     * @var TemplateProcessor
     */
    private $templateProcessor;

    /**
     * @var Template
     */
    private $template;

    /**
     * @var Replacer
     */
    private $replacer;

    /**
     * @var resource
     */
    private $tmpFile;

    /**
     * @var DocumentSaver
     */
    private $documentSaver;

    /**
     * @var SpecialTemplateRepository
     */
    private $specialTemplateRepository;

    /**
     * @var HtmlToDocxConverter
     */
    private $htmlToDocxConverter;

    /**
     * DefaultGenerator constructor.
     *
     * @param Replacer                  $replacer
     * @param DocumentSaver             $saver
     * @param SpecialTemplateRepository $specialTemplateRepository
     * @param HtmlToDocxConverter       $converter
     */
    public function __construct(
        Replacer $replacer,
        DocumentSaver $saver,
        SpecialTemplateRepository $specialTemplateRepository,
        HtmlToDocxConverter $converter
    ) {
        $this->replacer = $replacer;
        $this->documentSaver = $saver;
        $this->specialTemplateRepository = $specialTemplateRepository;
        $this->htmlToDocxConverter = $converter;
    }

    /**
     * @param array    $values
     * @param int|null $applicationId
     *
     * @return string
     *
     * @throws ClientExceptionInterface
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function generateDocxWithValues(array $values, ?int $applicationId): string
    {
        $this->tmpFile = tmpfile();
        $this->writeTemplateToTemp($applicationId);
        $tempFilePath = stream_get_meta_data($this->tmpFile)['uri'];
        $this->templateProcessor = new TemplateProcessor($tempFilePath);

        $allParams = $this->template->getArrayOfAllParameters();
        foreach ($values as $param => $value) {
            if (!isset($allParams[$param])) {
                continue;
            }

            $this->replacer->setStrategyByType($allParams[$param]->getType())
                ->replacePlaceHolderInDocx($this->templateProcessor, $allParams[$param], $value);
        }
        $this->templateProcessor->saveAs($tempFilePath);

        return $this->documentSaver->save($this->tmpFile, $this->template->getName().'.docx');
    }

    /**
     * @return string
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     * @throws Exception
     */
    public function generateTemplateFile(): string
    {
        $this->tmpFile = tmpfile();
        $this->writeTemplateToTemp(0);
        $tempFilePath = stream_get_meta_data($this->tmpFile)['uri'];
        $this->templateProcessor = new TemplateProcessor($tempFilePath);

        $this->templateProcessor->saveAs($tempFilePath);

        return $tempFilePath;
    }

    /**
     * Генерирует html из шаблона с плейсхолдерами, заменяя плейсхолдеры на нередактируемые инпуты.
     *
     * @param string $html
     * @param array  $values
     *
     * @return string
     *
     * @throws Exception
     */
    public function generateHtmlWithInputs(string $html, array $values): string
    {
        $allParams = $this->template->getArrayOfAllParameters();
        foreach ($values as $param => $value) {
            if (!isset($allParams[$param])) {
                continue;
            }

            $html = $this->replacer->setStrategyByType($allParams[$param]->getType())
                ->replacePlaceholderInHtml($html, $param, $value);
        }

        return $html;
    }

    /**
     * Генерирует html из html-строки с инпутами, заменяя инпуты на плейсхолдеры.
     *
     * @param string $html
     *
     * @return string
     *
     * @throws Exception
     */
    public function generateHtmlWithPlaceholders(string $html): string
    {
        $allParams = $this->template->getArrayOfAllParameters();

        foreach ($allParams as $templateParameter) {
            $html = $this->replacer->setStrategyByType($templateParameter->getType())
                ->replaceInputInHtml($html, $templateParameter);
        }

        return $html;
    }

    /**
     * @param Template $template
     *
     * @return DefaultGenerator
     */
    public function setTemplate(Template $template): GeneratorInterface
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Записывает шаблон во временный файл,.
     *
     * @param int|null $applicationId
     *
     * @throws Exception
     */
    private function writeTemplateToTemp(?int $applicationId)
    {
        if (null !== $applicationId && true === $this->template->getEditable()) {
            $specialTemplate = $this->specialTemplateRepository->findOneBy([
                'applicationId' => $applicationId,
                'template' => $this->template->getId(),
            ]);

            if (null !== $specialTemplate) {
                $html = base64_decode($specialTemplate->getBody());
                $converterType = ConverterConfigHandler::getConverterType($this->template->getHtmlTemplate());
                $this->tmpFile = $this->htmlToDocxConverter
                    ->init($html, $converterType)
                    ->convertToTemp();

                return;
            }
        }

        fwrite($this->tmpFile, $this->template->getFile());
    }

    public function __destruct()
    {
        fclose($this->tmpFile);
    }
}
