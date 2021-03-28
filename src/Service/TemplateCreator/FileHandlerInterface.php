<?php

declare(strict_types=1);

namespace App\Service\TemplateCreator;

interface FileHandlerInterface
{
    public function initFile($file);

    public function findParamPlaceholder(string $paramName): bool;

    public function getTempVariables(): array;

    public function saveFile(string $fileName, string $fileContent): bool;
}
