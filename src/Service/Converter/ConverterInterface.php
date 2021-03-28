<?php

declare(strict_types=1);

namespace App\Service\Converter;

interface ConverterInterface
{
    public function init(string $html, string $converterType): ConverterInterface;

    public function convertToDirWithName(string $directory, string $name): string;

    public function convertToTemp();
}
