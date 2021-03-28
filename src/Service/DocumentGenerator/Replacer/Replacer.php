<?php

declare(strict_types=1);

namespace App\Service\DocumentGenerator\Replacer;

use App\Entity\TemplateParameter;
use PhpOffice\PhpWord\TemplateProcessor;

class Replacer
{
    /**
     * @var ReplaceStrategyInterface
     */
    private $strategy;

    /**
     * @var StrategyPool
     */
    private $strategyPool;

    public function __construct(StrategyPool $strategyPool)
    {
        $this->strategyPool = $strategyPool;
    }

    /**
     * @param string $paramType
     *
     * @return Replacer
     *
     * @throws \Exception
     */
    public function setStrategyByType(string $paramType): Replacer
    {
        $this->strategy = $this->strategyPool->get($paramType);

        return $this;
    }

    /**
     * @param TemplateProcessor $templateProcessor
     * @param TemplateParameter $param
     * @param                   $value
     *
     * @throws \Exception
     */
    public function replacePlaceHolderInDocx(
        TemplateProcessor $templateProcessor,
        TemplateParameter $param,
        $value): void
    {
        $this->strategy->setTemplateProcessor($templateProcessor);
        if (!$this->strategy->checkValue($value)) {
            throw new \Exception('Некорректное значение для переменной '.$param->getAlias(), 400);
        }
        $this->strategy->replaceInDocx($param, $value);
    }

    /**
     * Заменяет плейсхолдер на инпут
     *
     * @param string $html
     * @param string $param
     * @param $value
     *
     * @return string
     *
     * @throws \Exception
     */
    public function replacePlaceholderInHtml(string $html, string $param, $value): string
    {
        $this->strategy->setHtml($html);
        if (!$this->strategy->checkValue($value)) {
            throw new \Exception('Некорректное значение для переменной '.$param, 400);
        }
        $this->strategy->replaceToInput($param, $value);

        return $this->strategy->getHtml();
    }

    /**
     * Заменяет инпут на плейсхолдер
     *
     * @param string            $html
     * @param TemplateParameter $templateParameter
     *
     * @return string
     */
    public function replaceInputInHtml(string $html, TemplateParameter $templateParameter): string
    {
        $this->strategy->setHtml($html)
            ->replaceToPlaceholder($templateParameter);

        return $this->strategy->getHtml();
    }
}
