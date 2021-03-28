<?php

declare(strict_types=1);


namespace App\Service\Converter\DocumentFiller\Handler;


use App\Service\Converter\Exception\IncorrectLinkException;
use PhpOffice\PhpWord\Element\AbstractContainer;

class LinkHandler extends AbstractHandler implements HandlerInterface
{

    /**
     * @param \DOMNode          $node
     * @param AbstractContainer $container
     *
     * @throws \Exception
     */
    public function handle(\DOMNode $node, AbstractContainer $container): void
    {
        if (false === $node->hasAttribute('href') ||
            false === $this->isLinkCorrect($node->getAttribute('href'))) {
            throw new IncorrectLinkException($node->nodeValue);
        }

        $container->addLink($node->getAttribute('href'), $node->nodeValue);
    }

    /**
     * @param string $link
     *
     * @return bool
     */
    public function isLinkCorrect(string $link): bool
    {
        $regexp = '/(?<scheme>http[s]?):\/\/(?<domain>[\w\.-]+)(?<path>[^?$]+)?(?<query>[^#$]+)?[#]?(?<fragment>[^$]+)?/';

        return (bool) preg_match($regexp, $link);
    }
}