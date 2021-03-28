<?php

declare(strict_types=1);


namespace App\Service\Converter\DocumentFiller;


use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\NumberFormat;
use PhpOffice\PhpWord\Style\Language;

/**
 * Класс отвечает за заполнение макета БГ банка Пересвет в формате docx при конвертации из html
 *
 * Class BgLayoutPeresvetFiller
 * @package App\Service\Converter\DocumentFiller
 */
class BgLayoutPeresvetFiller extends AbstractPhpWordFiller implements PhpWordFillerInterface
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
            'marginTop' => Converter::inchToTwip(0.45),
            'pageSizeH' => Converter::inchToTwip(11),
            'pageSizeW' => Converter::inchToTwip(8.5),
            'pageNumberingStart' => 1,
        ]);
        $this->section->addFooter()->addPreserveText('{PAGE}', null, [
            'alignment' => 'right',
        ]);
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(10);
        $phpWord->setDefaultParagraphStyle([
            'align' => 'both',
            'indentation' => [
                'firstLine' => Converter::inchToTwip(0.39)
            ],
        ]);
        $phpWord->addNumberingStyle(
            'default',
            [
                'type' => 'singleLevel',
                'levels' => [[
                    'left' => Converter::inchToTwip(0.39),
                    'suffix' => 'space',
                    'text' => '—',
                    'format' => NumberFormat::BULLET,
                ]]
            ]);
        $phpWord->addTitleStyle(2, ['size' => 10, 'bold' => true], [
            'alignment' => 'center',
            'spaceAfter' => Converter::pixelToTwip(10),
        ]);
        $phpWord->getSettings()->setThemeFontLang(new Language(Language::RU_RU));

        $this->phpWord = $phpWord;
    }
}
