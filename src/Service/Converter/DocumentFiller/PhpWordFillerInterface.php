<?php

declare(strict_types=1);

namespace App\Service\Converter\DocumentFiller;

use PhpOffice\PhpWord\PhpWord;
use Symfony\Component\DomCrawler\Crawler;

interface PhpWordFillerInterface
{
    public function init(PhpWord $phpWord);

    public function fillDocx(Crawler $crawler);
}
