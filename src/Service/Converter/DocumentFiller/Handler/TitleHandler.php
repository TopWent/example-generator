<?php

declare(strict_types=1);

namespace App\Service\Converter\DocumentFiller\Handler;

use PhpOffice\PhpWord\Element\AbstractContainer;

class TitleHandler extends AbstractHandler implements HandlerInterface
{
    /**
     * @param \DOMNode          $node
     * @param AbstractContainer $container
     */
    public function handle(\DOMNode $node, AbstractContainer $container): void
    {
        $container->addTitle($node->nodeValue, 2);
    }
}
