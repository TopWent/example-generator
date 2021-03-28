<?php

declare(strict_types=1);

namespace App\Service\Converter\DocumentFiller\Handler;

use PhpOffice\PhpWord\Element\AbstractContainer;
use PhpOffice\PhpWord\Element\Cell;
use PhpOffice\PhpWord\Shared\Converter;

class ParagraphHandler extends AbstractHandler implements HandlerInterface
{
    /**
     * @param \DOMNode          $node
     * @param AbstractContainer $container
     */
    public function handle(\DOMNode $node, AbstractContainer $container): void
    {
        $styleRaw = $this->getStyle($node);
        $style = [];

        if (isset($styleRaw['margin-top'])) {
            $style['spaceBefore'] = Converter::cssToTwip($styleRaw['margin-top']);
        }
        if (isset($styleRaw['margin-bottom'])) {
            $style['spaceAfter'] = Converter::cssToTwip($styleRaw['margin-bottom']);
        }
        if (isset($styleRaw['text-align'])) {
            $style['align'] = $styleRaw['text-align'];
        }
        if ($container instanceof Cell) {
            $style['indentation'] = [
                'firstLine' => Converter::inchToTwip(0.01),
            ];
        }

        $paragraph = $container->addTextRun($style);
        foreach ($node->childNodes as $childNode) {
            (new SpanHandler())->handle($childNode, $paragraph);
        }
    }
}
