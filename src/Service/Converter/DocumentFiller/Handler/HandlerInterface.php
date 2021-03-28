<?php

declare(strict_types=1);

namespace App\Service\Converter\DocumentFiller\Handler;

use PhpOffice\PhpWord\Element\AbstractContainer;

interface HandlerInterface
{
    public function handle(\DOMNode $node, AbstractContainer $container): void;
}
