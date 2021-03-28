<?php

declare(strict_types=1);

namespace App\Service\Converter\DocumentFiller\Handler;

use PhpOffice\PhpWord\Element\AbstractContainer;
use PhpOffice\PhpWord\Element\Cell;
use PhpOffice\PhpWord\Shared\Converter;

class SpanHandler extends AbstractHandler implements HandlerInterface
{
    public function handle(\DOMNode $node, AbstractContainer $container): void
    {
        if (ctype_space($node->nodeValue)) {
            return;
        }
        $styleRaw = $this->getStyle($node);
        $style = [];

        if (isset($styleRaw['font-weight'])) {
            if ('bold' === $styleRaw['font-weight']) {
                $style['bold'] = true;
            }
        }
        if (isset($styleRaw['font-style']) && 'italic' === $styleRaw['font-style']) {
            $style['italic'] = true;
        }

        if ($container instanceof Cell) {
            $container->addText($node->nodeValue, $style, [
                'indentation' => [
                    'firstLine' => Converter::inchToTwip(0.01),
                ]
            ]);
        } else {
            $container->addText($node->nodeValue, $style);
        }
    }
}
