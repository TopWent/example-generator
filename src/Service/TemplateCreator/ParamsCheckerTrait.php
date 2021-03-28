<?php

declare(strict_types=1);

namespace App\Service\TemplateCreator;

use App\Entity\TemplateParameter;

trait ParamsCheckerTrait
{
    /**
     * @return void
     *
     * @throws \Exception
     */
    private function isMatchParamsAndPlaceholders(): void
    {
        // достаем все параметры шаблона, а не только первых родителей
        $allParameters = $this->template->getArrayOfAllParameters();

        foreach ($allParameters as $key => $templateParameter) {
            if (!$this->fileHandler->findParamPlaceholder($templateParameter->getAlias())) {
                throw new \Exception('В документе не найден параметр '.$templateParameter->getAlias());
            }

            if (TemplateParameter::TYPE_BLOCK === $templateParameter->getType() &&
                !$this->fileHandler->findParamPlaceholder('/'.$templateParameter->getAlias())) {
                throw new \Exception('В документе не найден закрывающий тег блока '.$templateParameter->getAlias(), 400);
            }
            unset($allParameters[$key]);
        }

        if (!empty($allParameters)) {
            throw new \Exception('В документе меньше переменных, чем задано в параметрах.');
        }

        if (!empty($this->fileHandler->getTempVariables())) {
            throw new \Exception('В параметрах не описаны переменные: '.implode(', ', $this->fileHandler->getTempVariables()));
        }
    }
}
