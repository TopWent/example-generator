<?php

declare(strict_types=1);

namespace App\Service\DocumentGenerator\Replacer;

use App\Entity\TemplateParameter;

class ReplaceBlockStrategy extends AbstractReplaceStrategy
{
    /**
     * @param $value
     *
     * @return bool
     */
    public function checkValue($value): bool
    {
        if (!is_array($value) && !is_string($value) && !empty($value)) {
            return false;
        }

        if (is_array($value)) {
            foreach ($value as $item) {
                if (!is_array($item)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param TemplateParameter $parameter
     * @param                   $value
     */
    public function replaceInDocx(TemplateParameter $parameter, $value): void
    {
        // если пришло значение "false", то удаляем блок
        if (false === $value) {
            $this->templateProcessor->cloneBlock($parameter->getAlias(), 0);
        // если пришло любое другое пустое значение, то заменяем каждый плейсхолдер внутри блока на пустое значение
        } elseif (empty($value)) {
            $this->templateProcessor->cloneBlock(
                $parameter->getAlias(),
                0,
                true,
                false,
                $this->replaceChildParamsToEmptyValues($parameter));
        }
        // если пришел массив, то заменяем плейсхолдеры на значения и клонируем блоки
        elseif (is_array($value)) {
            $this->templateProcessor->cloneBlock(
                $parameter->getAlias(),
                0,
                true,
                false,
                $value);
        }
    }

    /**
     * @param string $param
     * @param $value
     */
    public function replaceToInput(string $param, $value): void
    {
        // сейчас не умеет клонировать блоки, нужно будет допиливать
        if (is_array($value)) {
            $this->html = str_replace('<p>${'.$param.'}</p>', '<div class="'.$param.'">', $this->html);
            $this->html = str_replace('<p>${/'.$param.'}</p>', '</div>', $this->html);
            foreach ($value as $block) {
                foreach ($block as $placeholder => $inputValue) {
                    $this->html = str_replace(
                        '${'.$placeholder.'}',
                        $this->createInput($placeholder, $inputValue),
                        $this->html);
                }
            }
        }

        $pattern = '/<p>\$\{'.$param.'\}((.|\n)*?|)\$\{\/'.$param.'\}<\/p>/';

        if (is_string($value)) {
            $this->html = preg_replace(
                $pattern,
                '<div class="'.$param.'">'.$value.'</div>',
                $this->html);
        }

        if (false === $value) {
            $this->html = preg_replace(
                $pattern,
                '<div class="'.$param.'"></div>',
                $this->html
            );
        }
    }

    /**
     * @param TemplateParameter $templateParameter
     */
    public function replaceToPlaceholder(TemplateParameter $templateParameter): void
    {
        $param = $templateParameter->getAlias();
        $pattern = '/<div class="'.$param.'">((.|\n)*?|)<\/div>/';

        if (!empty($templateParameter->getChildren())) {
            $this->html = preg_replace_callback($pattern, function ($matches) use ($templateParameter) {
                $result = preg_replace(
                    '/^<div class="'.$templateParameter->getAlias().'">/',
                    '<p>${'.$templateParameter->getAlias().'}</p>',
                    $matches[0]);
                $result = preg_replace(
                    '/<\/div>$/',
                    '<p>${/'.$templateParameter->getAlias().'}</p>',
                    $result);

                foreach ($templateParameter->getChildren() as $childParam) {
                    $childPattern = $this->getInputPattern($childParam->getAlias());
                    $result = preg_replace(
                        $childPattern,
                        '<p>${'.$childParam->getAlias().'}</p>',
                        $result
                    );
                }

                return $result;
            }, $this->html);
        }
    }

    /**
     * @param TemplateParameter $parameter
     *
     * @return array
     */
    private function replaceChildParamsToEmptyValues(TemplateParameter $parameter): array
    {
        $childParams = $parameter->getChildren();
        $result = [];

        foreach ($childParams as $childParam) {
            $result[$childParam->getAlias()] = '';
        }

        return [$result];
    }
}
