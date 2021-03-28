<?php

declare(strict_types=1);

namespace App\Service\DocumentGenerator\Replacer;

use App\Entity\TemplateParameter;
use PhpOffice\PhpWord\TemplateProcessor;

abstract class AbstractReplaceStrategy implements ReplaceStrategyInterface
{
    protected const NOT_EDITABLE_CLASS = 'not-editable';
    /**
     * @var TemplateProcessor
     */
    protected $templateProcessor;

    /**
     * @var string
     */
    protected $html;

    /**
     * @param TemplateProcessor $templateProcessor
     */
    public function setTemplateProcessor(TemplateProcessor $templateProcessor): void
    {
        $this->templateProcessor = $templateProcessor;
    }

    /**
     * @param string $param
     *
     * @return string
     */
    protected function getInputPattern(string $param)
    {
        return '/<input class="'.self::NOT_EDITABLE_CLASS.'" name="'.$param.'" value="(.*?|)" \/>/';
    }

    /**
     * @param string $param
     * @param $value
     *
     * @return string
     */
    protected function createInput(string $param, $value)
    {
        return '<input class="'.self::NOT_EDITABLE_CLASS.'" name="'.$param.'" value="'.$value.'" \/>';
    }

    /**
     * @param string $html
     *
     * @return ReplaceStrategyInterface
     */
    public function setHtml(string $html): ReplaceStrategyInterface
    {
        $this->html = $html;

        return $this;
    }

    /**
     * @return string
     */
    public function getHtml(): string
    {
        return $this->html;
    }

    /**
     * Проверяет корректное ли значение пришло для плейсхолдера данного типа.
     *
     * @param $value
     *
     * @return bool
     */
    abstract public function checkValue($value): bool;

    /**
     * Заменяет плейсхолдеры на значения в файле docx.
     *
     * @param TemplateParameter $parameter
     * @param                   $value
     */
    abstract public function replaceInDocx(TemplateParameter $parameter, $value): void;

    /**
     * Заменяет плейсхолдеры в html на нередактируемые инпуты со значениями.
     *
     * @param string $param
     * @param $value
     */
    abstract public function replaceToInput(string $param, $value): void;

    /**
     * Заменяет инпуты в html обратно в плейсхолдеры.
     *
     * @param TemplateParameter $templateParameter
     */
    abstract public function replaceToPlaceholder(TemplateParameter $templateParameter): void;
}
