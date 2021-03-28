<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\HttpFoundation\File\File as SymfonyFile;
use RuntimeException;

/**
 * Класс добавляет функциональность в стандартный класс File.
 */
class File extends SymfonyFile
{
    /**
     * Метод получает контент файла.
     *
     * @return string
     */
    public function getInBase64(): string
    {
        return base64_encode($this->getContent());
    }

    /**
     * Метод получает контент файла в base64.
     *
     * @return string
     */
    public function getContent(): string
    {
        $file = $this->openFile();

        if (true === $file->eof()) {
            throw new RuntimeException('Файл пуст');
        }

        $content = '';

        while (!$file->eof()) {
            $content .= $file->fgets();
        }

        return $content;
    }
}
