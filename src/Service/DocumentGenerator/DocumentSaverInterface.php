<?php

declare(strict_types=1);

namespace App\Service\DocumentGenerator;

interface DocumentSaverInterface
{
    public function save($tmpFile, string $fileName): string;
}
