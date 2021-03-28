<?php

declare(strict_types=1);

namespace App\Service\Converter\DocumentFiller\Handler;

/**
 * Абстрактный обработчик элементов DOM-дерева.
 *
 * Class AbstractHandler
 * @package App\Service\Converter\DocumentFiller\Handler
 */
class AbstractHandler
{
    /**
     * Извлекает стили элемента DOM-дерева из атрибута style.
     *
     * @param \DOMNode $node
     *
     * @return array|null
     */
    protected function getStyle(\DOMNode $node): ?array
    {
        if ($node->hasAttributes()) {
            return $this->parseStyleAttribute($node->getAttribute('style')) ?? null;
        }

        return null;
    }

    /**
     * Парсит строку из атрибута style.
     *
     * @param string $style
     *
     * @return array
     */
    protected function parseStyleAttribute(string $style): array
    {
        $result = [];
        preg_match_all('/([a-z|-]*):\s*([^;]*)/', $style, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $result[$match[1]] = $match[2];
        }

        return $result;
    }
}
