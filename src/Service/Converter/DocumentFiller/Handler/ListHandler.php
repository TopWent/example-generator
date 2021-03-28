<?php

declare(strict_types=1);

namespace App\Service\Converter\DocumentFiller\Handler;

use PhpOffice\PhpWord\Element\AbstractContainer;
use PhpOffice\PhpWord\Shared\Converter;

/**
 * Отвечает за обработку списков (DOM-элементов с тегом li)
 *
 * Class ListHandler
 * @package App\Service\Converter\DocumentFiller\Handler
 */
class ListHandler extends AbstractHandler implements HandlerInterface
{
    private $level = 0;

    /**
     * @var HandlerFactory
     */
    private $handlerFactory;

    /**
     * @var AbstractContainer
     */
    private $listItemRun;

    public function __construct(HandlerFactory $handlerFactory)
    {
        $this->handlerFactory = $handlerFactory;
    }

    /**
     * Обрабатывает DOM-объект с тегом li
     *
     * @param \DOMNode          $node
     * @param AbstractContainer $container
     */
    public function handle(\DOMNode $node, AbstractContainer $container): void
    {
        if ($node->hasAttributes() && 'multilevel' === $node->getAttribute('class')) {
            $this->iterateMultilevel($node, $container, 'multilevel');
        } else {
            $this->iterateMultilevel($node, $container);
        }
    }

    /**
     * Обрабатывает каждый элемент списка и его потомков рекурсивно
     *
     * @param \DOMNode          $node
     * @param AbstractContainer $container
     * @param string            $styleType
     */
    public function iterateMultilevel(
        \DOMNode $node,
        AbstractContainer $container,
        string $styleType = 'default'
    ) {
        foreach ($node->childNodes as $childNode) {
            if (ctype_space($childNode->nodeValue)) {
                continue;
            }
            if ('li' === $childNode->nodeName) {
                $rawStyle = $this->getStyle($childNode);
                $pStyle = [];

                if (isset($rawStyle['text-align'])) {
                    $pStyle['alignment'] = $rawStyle['text-align'];
                }
                if (isset($rawStyle['margin-top'])) {
                    $pStyle['spaceBefore'] = Converter::pixelToTwip(
                        str_replace('px', '', $rawStyle['margin-top'])
                    );
                }
                if ($childNode->hasChildNodes()) {
                    $this->listItemRun = $container->addListItemRun($this->level, $styleType, $pStyle);
                    $this->iterateMultilevel($childNode, $container, $styleType);
                    continue;
                }
            }
            if ('ol' === $childNode->nodeName) {
                ++$this->level;
                $this->iterateMultilevel($childNode, $container, $styleType);
                --$this->level;
                continue;
            }
            $handler = $this->handlerFactory->createHandler($childNode->nodeName);
            if (null !== $handler) {
                $handler->handle(
                    $childNode,
                    in_array($childNode->nodeName, ['table', 'p']) ? $container : $this->listItemRun
                );
            }
        }
    }
}
