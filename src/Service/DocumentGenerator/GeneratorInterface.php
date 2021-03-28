<?php

declare(strict_types=1);

namespace App\Service\DocumentGenerator;

use App\Entity\Template;

interface GeneratorInterface
{
    /**
     * @param Template $template
     *
     * @return GeneratorInterface
     */
    public function setTemplate(Template $template): GeneratorInterface;

    /**
     * Генерирует шаблон docx.
     *
     * @return string
     */
    public function generateTemplateFile(): string;

    /**
     * Генерирует итоговый документ, заменяя плейсхолдеры на значения.
     *
     * @param array $values
     *
     * @return string
     */
    public function generateDocxWithValues(array $values, ?int $applicationId): string;

    /**
     * Генерирует html из шаблона с плейсхолдерами, заменяя плейсхолдеры на нередактируемые инпуты.
     *
     * @param string $html
     * @param array  $values
     *
     * @return string
     */
    public function generateHtmlWithInputs(string $html, array $values): string;

    /**
     * Генерирует html из html-строки с инпутами, заменяя инпуты на плейсхолдеры.
     *
     * @param string $html
     *
     * @return string
     */
    public function generateHtmlWithPlaceholders(string $html): string;
}
