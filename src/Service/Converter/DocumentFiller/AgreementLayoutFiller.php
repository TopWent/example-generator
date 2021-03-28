<?php

declare(strict_types=1);

namespace App\Service\Converter\DocumentFiller;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\NumberFormat;
use PhpOffice\PhpWord\Style\Language;

/**
 * Класс отвечает за заполнение макета соглашения в формате docx при конвертации из html
 *
 * Class AgreementLayoutFiller
 * @package App\Service\Converter\DocumentFiller
 */
class AgreementLayoutFiller extends AbstractPhpWordFiller implements PhpWordFillerInterface
{
    /**
     * @param PhpWord $phpWord
     */
    public function init(PhpWord $phpWord)
    {
        $this->section = $phpWord->addSection([
            'marginLeft' => Converter::inchToTwip(0.79),
            'marginRight' => Converter::inchToTwip(0.59),
            'marginBottom' => Converter::inchToTwip(0.49),
            'marginTop' => Converter::inchToTwip(0.39),
            'pageNumberingStart' => 1,
            'pageSizeH' => Converter::inchToTwip(11),
            'pageSizeW' => Converter::inchToTwip(8.5),
        ]);
        $this->section->addFooter()->addPreserveText('{PAGE}', null, [
            'alignment' => 'right',
        ]);
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(9);
        $phpWord->setDefaultParagraphStyle([
            'align' => 'both',
            'indentation' => [
                'firstLine' => Converter::inchToTwip(0.30)
            ],
        ]);
        $phpWord->addTitleStyle(2, ['size' => 9, 'bold' => true], [
            'alignment' => 'center',
            'spaceAfter' => Converter::pixelToTwip(20),
        ]);
        $phpWord->getSettings()->setThemeFontLang(new Language(Language::RU_RU));
        $phpWord->addNumberingStyle(
            'default',
            [
                'type' => 'singleLevel',
                'levels' => [[
                    'suffix' => 'tab',
                    'text' => '•',
                    'format' => NumberFormat::BULLET,
                ]]
            ]);
        $phpWord->addNumberingStyle(
            'multilevel',
            [
                'type' => 'multilevel',
                'levels' => [
                    ['format' => NumberFormat::DECIMAL, 'text' => '%1.', 'suffix' => 'space', 'bold' => true],
                    ['format' => NumberFormat::DECIMAL, 'text' => '%1.%2.', 'suffix' => 'space', 'bold' => true],
                    ['format' => NumberFormat::DECIMAL, 'text' => '%1.%2.%3.', 'suffix' => 'nothing', 'bold' => true],
                ],
            ]
        );

        $this->phpWord = $phpWord;
    }
}
