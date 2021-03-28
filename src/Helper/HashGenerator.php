<?php

declare(strict_types=1);

namespace App\Helper;

class HashGenerator
{
    public static function generate(string $string): string
    {
        return md5($string);
    }
}
