<?php

declare(strict_types=1);

namespace App\Service\DocumentGenerator\Replacer;

use App\Entity\TemplateParameter;

class ReplaceRowStrategy extends AbstractReplaceStrategy
{
    /**
     * @param $value
     *
     * @return bool
     */
    public function checkValue($value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        foreach ($value as $item) {
            if (!is_array($item)) {
                return false;
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
        $this->templateProcessor->cloneRowAndSetValues($parameter->getAlias(), $value);
    }

    /**
     * @param string $param
     * @param $value
     *
     * @throws \Exception
     */
    public function replaceToInput(string $param, $value): void
    {
        $pattern = '/<tr((?!<tr).|\n)*?\$\{'.$param.'\}((.|\n)*?|)<\/tr>/';

        // находим по паттерну строку в таблице и запоминаем место, где она начинается
        preg_match($pattern, $this->html, $matches, PREG_OFFSET_CAPTURE);
        $rowWithPlaceHolders = $matches[0][0] ?? null;

        if (null === $rowWithPlaceHolders) {
            throw new \Exception('В редактируемом шаблоне не найдена строка '.$param);
        }
        $rowPlaceInHtml = $matches[0][1];

        $this->fillRowsOfInputs($param, $value, $rowPlaceInHtml, $rowWithPlaceHolders);
    }

    /**
     * @param TemplateParameter $templateParameter
     *
     * @throws \Exception
     */
    public function replaceToPlaceholder(TemplateParameter $templateParameter): void
    {
        $pattern = '/<tr class="'.$templateParameter->getAlias().'"((?!<tr).|\n)*?((.|\n)*?|)<\/tr>/';

        // находим по паттерну строку в таблице
        preg_match_all($pattern, $this->html, $matches);
        $rowsWithInputs = $matches[0] ?? null;
        if (empty($rowsWithInputs)) {
            throw new \Exception('В отредактированном шаблоне нет строк для '.$templateParameter->getAlias());
        }
        // удаляем все строки в таблице, кроме первой
        $this->removeUnusableRows($rowsWithInputs);

        // подставляем в первую строку плейсхолдеры вместо инпутов
        $inputPattern = $this->getInputPattern($templateParameter->getAlias());
        $rowWithPlaceholders = preg_replace(
            $inputPattern,
            '${'.$templateParameter->getAlias().'}',
            $rowsWithInputs[0]
        );
        if (!empty($templateParameter->getChildren())) {
            foreach ($templateParameter->getChildren() as $childParam) {
                $childPattern = $this->getInputPattern($childParam->getAlias());
                $rowWithPlaceholders = preg_replace(
                    $childPattern,
                    '${'.$childParam->getAlias().'}',
                    $rowWithPlaceholders
                );
            }
        }

        $this->html = str_replace($rowsWithInputs[0], $rowWithPlaceholders, $this->html);
    }

    /**
     * Заполняет строку инпутами со значениями и размножает строки в html-шаблоне.
     *
     * @param string $param
     * @param array  $rows
     * @param int    $rowPlaceInHtml
     * @param string $rowWithPlaceHolders
     */
    private function fillRowsOfInputs(string $param, array $rows, int $rowPlaceInHtml, string $rowWithPlaceHolders)
    {
        for ($i = 0; $i < count($rows); ++$i) {
            // заменяем в строке с плейсхолдерами все плейсхолдеры на инпуты
            $rowWithInputs = $rowWithPlaceHolders;
            foreach ($rows[$i] as $placeholder => $inputValue) {
                $rowWithInputs = str_replace(
                    '${'.$placeholder.'}',
                    $this->createInput($placeholder, $inputValue),
                    $rowWithInputs);
            }
            // добавляем строке класс, чтобы потом найти ее
            $rowWithInputs = substr($rowWithInputs, 0, 4).
                'class="'.$param.'" '.
                substr($rowWithInputs, 4);

            $this->html = str_replace($rowWithPlaceHolders, $rowWithInputs, $this->html);
            $endOfRow = $rowPlaceInHtml + strlen($rowWithInputs);

            // если это еще не последняя строка, то добавляем в таблицу еще строку с плейсхолдерами
            if ($i < count($rows) - 1) {
                $this->html = substr($this->html, 0, $endOfRow + 1).
                    $rowWithPlaceHolders.
                    substr($this->html, $endOfRow + 1);
            }
        }
    }

    /**
     * @param array $rowsWithInputs
     */
    private function removeUnusableRows(array $rowsWithInputs)
    {
        if (count($rowsWithInputs) > 1) {
            for ($i = 1; $i < count($rowsWithInputs); ++$i) {
                $this->html = str_replace($rowsWithInputs[$i], '', $this->html);
            }
        }
    }
}
