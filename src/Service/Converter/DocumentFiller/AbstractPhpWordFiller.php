<?php

declare(strict_types=1);

namespace App\Service\Converter\DocumentFiller;

use App\Service\Converter\DocumentFiller\Handler\HandlerFactory;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Component\DomCrawler\Crawler;

class AbstractPhpWordFiller
{
    private const AVAILABLE_TAGS = [
        'h2', '#text', 'table', 'p', 'span', 'ul', 'ol', 'a',
    ];

    /**
     * @var PhpWord
     */
    protected $phpWord;

    /**
     * @var Section
     */
    protected $section;

    /**
     * @var HandlerFactory
     */
    protected $handlerFactory;

    public function __construct(HandlerFactory $handlerFactory)
    {
        $this->handlerFactory = $handlerFactory;
    }

    public function fillDocx(Crawler $crawler)
    {
        foreach ($crawler->getIterator() as $node) {
            if (in_array($node->nodeName, self::AVAILABLE_TAGS)) {
                $this->handleNode($node);
            } else {
                $this->handleChildNodes($node);
            }
        }
    }

    private function handleChildNodes(\DOMNode $node)
    {
        if (!is_iterable($node->childNodes)) {
            return;
        }
        foreach ($node->childNodes as $childNode) {
            if (in_array($childNode->nodeName, self::AVAILABLE_TAGS)) {
                $this->handleNode($childNode);
            } else {
                $this->handleChildNodes($childNode);
            }
        }
    }

    private function handleNode(\DOMNode $node): bool
    {
        $handler = $this->handlerFactory->createHandler($node->nodeName);

        if (null !== $handler) {
            $handler->handle($node, $this->section);
            unset($handler);

            return true;
        }

        return false;
    }
}
