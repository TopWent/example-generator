<?php

declare(strict_types=1);

namespace App\Service\Converter\DocumentFiller\Handler;

use PhpOffice\PhpWord\Element\AbstractContainer;
use PhpOffice\PhpWord\Element\Cell as CellContainer;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\Style\Cell;

class TableHandler extends AbstractHandler implements HandlerInterface
{
    /**
     * @var HandlerFactory
     */
    private $handlerFactory;

    /**
     * Номер колонки в которой будет объединение ячеек.
     *
     * @var int
     */
    private $colNum;

    /**
     * Количество ячеек для объединения.
     *
     * @var int
     */
    private $rowspan;

    /**
     * Вертикальное выравнивание текста
     *
     * @var string
     */
    private $vAlign;

    private $cellBackgroundColor;

    public function __construct(HandlerFactory $handlerFactory)
    {
        $this->handlerFactory = $handlerFactory;
    }

    /**
     * @param \DOMNode          $node
     * @param AbstractContainer $container
     */
    public function handle(\DOMNode $node, AbstractContainer $container): void
    {
        $styleRaw = $this->getStyle($node);
        $style = [
            'borderSize' => 1,
            'width' => 50 * 100,
            'unit' => TblWidth::PERCENT,
            'alignment' => 'center',
            'cellMarginLeft' => Converter::inchToTwip(0.01),
            'cellMarginRight' => Converter::inchToTwip(0.01),
            'cellMarginTop' => Converter::inchToTwip(0.03),
            'cellMarginBottom' => Converter::inchToTwip(0.01),
        ];

        if (isset($styleRaw['border-color'])) {
            $style['borderColor'] = str_replace('#', '', $styleRaw['border-color']);
        }
        if (isset($styleRaw['vertical-align'])) {
            $this->vAlign = $styleRaw['vertical-align'];
        }
        if (isset($styleRaw['margin-top'])) {
            $style['spaceBefore'] = Converter::cssToTwip($styleRaw['margin-top']);
        }

        $table = $container->addTable($style);
        foreach ($node->childNodes as $childNode) {
            $this->handleTableRow($childNode, $table);
        }
    }

    /**
     * @param \DOMNode    $node
     * @param Table       $table
     */
    private function handleTableRow(\DOMNode $node, Table $table): void
    {
        $styleRaw = $this->getStyle($node);
        if (isset($styleRaw['background'])) {
            $this->cellBackgroundColor = str_replace('#', '', $styleRaw['background']);
        } else {
            $this->cellBackgroundColor = 'ffffff';
        }

        $table->addRow();

        if (!is_iterable($node->childNodes)) {
            return;
        }
        foreach ($node->childNodes as $colNum => $childNode) {
            if (!empty($this->rowspan) && $colNum === $this->colNum) {
                $this->continueRowSpan($table);
            }
            if ('td' === $childNode->nodeName) {
                $this->handleTableCell($childNode, $table, $colNum);
            }
        }
    }

    /**
     * @param \DOMNode    $node
     * @param Table       $table
     * @param int|null    $colNum
     */
    private function handleTableCell(\DOMNode $node, Table $table, ?int $colNum): void
    {
        $styleRaw = $this->getStyle($node);
        $style = [];

        $style['bgColor'] = $this->cellBackgroundColor;
        if (isset($styleRaw['width'])) {
            $style['width'] = str_replace('%', '', $styleRaw['width']);
        }
        if (isset($styleRaw['vertical-align'])) {
            $style['valign'] = $styleRaw['vertical-align'];
        } elseif (null !== $this->vAlign) {
            $style['valign'] = $this->vAlign;
        }

        if ($node->hasAttributes() && !empty($node->getAttribute('rowspan'))) {
            $this->colNum = $colNum;
            $this->rowspan = $node->getAttribute('rowspan') - 1;
            $cell = $table->addCell($style['width'] ?? null, ['vMerge' => Cell::VMERGE_RESTART]);
        } else {
            $cell = $table->addCell($style['width'] ?? 100, $style);
        }

        $this->handleChildNodes($node, $cell);
    }

    /**
     * Обрабатывает тэги внутри ячейки таблицы
     *
     * @param \DOMNode      $node
     * @param CellContainer $cell
     */
    private function handleChildNodes(\DOMNode $node, CellContainer $cell): void
    {
        /** @var \DOMNode $childNode */
        foreach ($node->childNodes as $childNode) {
            $handler = $this->handlerFactory->createHandler($childNode->nodeName);
            $handler->handle($childNode, $cell);
        }
    }

    /**
     * Продолжает объединение ячеек в колонке
     *
     * @param Table $table
     */
    private function continueRowSpan(Table $table): void
    {
        --$this->rowspan;
        $table->addCell(null, ['vMerge' => Cell::VMERGE_CONTINUE]);
    }
}
