<?php

declare(strict_types=1);

namespace App\Helper;

use Symfony\Component\Yaml\Yaml;

class ConverterConfigHandler
{
    public static function getConverterType(string $templateFile): ?string
    {
        $config = Yaml::parseFile($_ENV['CONVERTER_CONFIG']);

        return $config[$templateFile] ?? null;
    }
}
