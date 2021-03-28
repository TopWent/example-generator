<?php

declare(strict_types=1);

namespace App\Service\Converter\DocumentFiller;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\Language;

/**
 * Класс отвечает за заполнение макета банковской гарантии в формате docx при конвертации из html
 *
 * Class BgLayoutFiller
 * @package App\Service\Converter\DocumentFiller
 */
class BgLayoutFiller extends AbstractPhpWordFiller implements PhpWordFillerInterface
{
    /**
     * @param PhpWord $phpWord
     */
    public function init(PhpWord $phpWord)
    {
        $this->section = $phpWord->addSection([
            'marginLeft' => Converter::cmToTwip(1.5),
            'marginRight' => Converter::cmToTwip(1),
            'pageNumberingStart' => 1,
        ]);
        $this->section->addFooter()->addPreserveText('{PAGE}', null, [
            'alignment' => 'right',
        ]);
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(8);
        $phpWord->setDefaultParagraphStyle([
            'align' => 'both',
        ]);
        $phpWord->addTitleStyle(2, ['size' => 8, 'bold' => true], [
            'alignment' => 'center',
            'spaceAfter' => Converter::pixelToTwip(20),
        ]);
        $phpWord->getSettings()->setThemeFontLang(new Language(Language::RU_RU));

        $this->phpWord = $phpWord;
    }
}
