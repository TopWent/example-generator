<?php

declare(strict_types=1);

namespace App\Service\TemplateCreator;

use Exception;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use PhpOffice\PhpWord\TemplateProcessor;

class DocxFileHandler implements FileHandlerInterface
{
    /**
     * @var string
     */
    private $nativeTemplatesPath;

    /**
     * @var TemplateProcessor
     */
    private $templateProcessor;

    /**
     * В этом массиве храним список переменных для сверки с теми, что пришли в запросе.
     *
     * @var array
     */
    private $tempVariables;

    public function __construct(string $nativeTemplatesPath)
    {
        $this->nativeTemplatesPath = $nativeTemplatesPath;
    }

    /**
     * @param $tmpFile
     *
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     */
    public function initFile($tmpFile)
    {
        $tempFilePath = stream_get_meta_data($tmpFile)['uri'];
        $this->templateProcessor = new TemplateProcessor($tempFilePath);
        $this->tempVariables = $this->templateProcessor->getVariables();
    }

    /**
     * @param string $paramName
     *
     * @return bool
     */
    public function findParamPlaceholder(string $paramName): bool
    {
        $key = array_search($paramName, $this->tempVariables);

        if (false !== $key) {
            unset($this->tempVariables[$key]);

            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getTempVariables(): array
    {
        return $this->tempVariables;
    }

    /**
     * @param string $fileName
     *
     * @return string
     * @throws \Exception
     */
    public function generateFilePath(string $fileName): string
    {
        $path = $this->nativeTemplatesPath;

        if (empty($path)) {
            throw new Exception("Не задан путь для шаблонов документов NATIVE_TEMPLATES_PATH");
        }

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        return $path.$fileName.'.docx';
    }

    /**
     * @param string $filePath
     * @param string $fileContent
     *
     * @return bool
     */
    public function saveFile(string $filePath, string $fileContent): bool
    {
        if (file_put_contents($filePath, base64_decode($fileContent))) {
            return true;
        } else {
            return false;
        }
    }
}
