<?php

declare(strict_types=1);

namespace App\Service\DocumentGenerator\Replacer;

use App\Entity\TemplateParameter;

class StrategyPool
{
    /**
     * @var ReplaceStrategyInterface[]
     */
    private $strategies;

    /**
     * @param string $type
     *
     * @return ReplaceStrategyInterface
     *
     * @throws \Exception
     */
    public function get(string $type): ReplaceStrategyInterface
    {
        if (!isset($this->strategies[$type])) {
            $this->strategies[$type] = $this->createStrategy($type);
        }

        return $this->strategies[$type];
    }

    /**
     * @param string $paramType
     *
     * @return ReplaceStrategyInterface
     *
     * @throws \Exception
     */
    private function createStrategy(string $paramType): ReplaceStrategyInterface
    {
        switch ($paramType) {
            case TemplateParameter::TYPE_SIMPLE:
                return new ReplaceSimpleStrategy();
            case TemplateParameter::TYPE_BLOCK:
                return new ReplaceBlockStrategy();
            case TemplateParameter::TYPE_ROW:
                return new ReplaceRowStrategy();
            default:
                throw new \Exception('Неизвестный тип параметра.');
        }
    }
}
