<?php

declare(strict_types=1);

namespace App\Service\DocumentGenerator\Replacer;

use App\Entity\TemplateParameter;

class ReplaceSimpleStrategy extends AbstractReplaceStrategy
{
    /**
     * @param $value
     *
     * @return bool
     */
    public function checkValue($value): bool
    {
        if (is_array($value) || is_object($value)) {
            return false;
        }

        return true;
    }

    /**
     * @param TemplateParameter $parameter
     * @param                   $value
     */
    public function replaceInDocx(TemplateParameter $parameter, $value): void
    {
        $this->templateProcessor->setValue($parameter->getAlias(), $value);
    }

    /**
     * @param string $param
     * @param $value
     */
    public function replaceToInput(string $param, $value): void
    {
        $this->html = str_replace('${'.$param.'}', $this->createInput($param, $value), $this->html);
    }

    /**
     * @param TemplateParameter $templateParameter
     */
    public function replaceToPlaceholder(TemplateParameter $templateParameter): void
    {
        $this->html = preg_replace(
            $this->getInputPattern($templateParameter->getAlias()),
            '${'.$templateParameter->getAlias().'}',
            $this->html);
    }
}
