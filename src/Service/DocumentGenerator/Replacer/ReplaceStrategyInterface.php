<?php

declare(strict_types=1);

namespace App\Service\DocumentGenerator\Replacer;

use App\Entity\TemplateParameter;
use PhpOffice\PhpWord\TemplateProcessor;

interface ReplaceStrategyInterface
{
    /**
     * Проверяет корректное ли значение пришло для плейсхолдера данного типа.
     *
     * @param $value
     *
     * @return bool
     */
    public function checkValue($value): bool;

    /**
     * Заменяет плейсхолдеры на значения в файле docx.
     *
     * @param TemplateParameter $parameter
     * @param                   $value
     */
    public function replaceInDocx(TemplateParameter $parameter, $value): void;

    /**
     * Заменяет плейсхолдеры в html на нередактируемые инпуты со значениями.
     *
     * @param string $param
     * @param $value
     */
    public function replaceToInput(string $param, $value): void;

    /**
     * Заменяет инпуты в html обратно в плейсхолдеры.
     *
     * @param TemplateParameter $templateParameter
     */
    public function replaceToPlaceholder(TemplateParameter $templateParameter): void;

    /**
     * @param TemplateProcessor $templateProcessor
     */
    public function setTemplateProcessor(TemplateProcessor $templateProcessor): void;

    /**
     * @param string $html
     *
     * @return ReplaceStrategyInterface
     */
    public function setHtml(string $html): ReplaceStrategyInterface;

    /**
     * @return string
     */
    public function getHtml(): string;
}
