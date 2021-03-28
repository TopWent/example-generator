<?php

declare(strict_types=1);

namespace App\Service\Converter\DocumentFiller\Handler;

class HandlerFactory
{
    /**
     * Создает обработчик для элемента DOM-дерева, чтобы извлечь его значение с сохранением структуры и стилей
     * документа.
     *
     * @param string $nodeName
     *
     * @return HandlerInterface|null
     */
    public function createHandler(string $nodeName): ?HandlerInterface
    {
        switch ($nodeName) {
            case 'h2':
                return new TitleHandler();
            case 'p':
                return new ParagraphHandler();
            case 'table':
                return new TableHandler($this);
            case 'span':
            case '#text':
                return new SpanHandler();
            case 'ul':
            case 'ol':
                return new ListHandler($this);
            case 'a':
                return new LinkHandler();
            default:
                return null;
        }
    }
}
