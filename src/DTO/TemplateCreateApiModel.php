<?php

declare(strict_types=1);

namespace App\DTO;

use App\Validator\Constraints as CustomAssert;
use Symfony\Component\Validator\Constraints as Assert;

class TemplateCreateApiModel
{
    /**
     * @var string $file Файл в кодировке base64
     * @Assert\NotBlank()
     */
    private $file;

    /**
     * @var string $alias Название-идентификатор шаблона на латинице
     * @Assert\NotBlank()
     */
    private $alias;

    /**
     * @var string $name Название шаблона в свободной форме, для отображения
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var array $params Массив с описанием параметров (плейсхолдеров) в шаблоне
     * @Assert\NotBlank()
     */
    private $params;

    /**
     * @var int $bankId ID банка. Может быть пустым для общих шаблонов.
     * @Assert\Type("integer")
     */
    private $bankId;

    /**
     * @var bool $editable Признак редактируемости
     * @Assert\Type("boolean")
     */
    private $editable = false;

    /**
     * @var string $htmlTemplate Название файла с редактируемым вариантом шаблона
     * @Assert\Type("string")
     * @CustomAssert\HtmlTemplateExists()
     */
    private $htmlTemplate;

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @param string $file
     */
    public function setFile(string $file): void
    {
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     */
    public function setAlias(string $alias): void
    {
        $this->alias = $alias;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    /**
     * @return int|null
     */
    public function getBankId(): ?int
    {
        return $this->bankId;
    }

    /**
     * @param int|null $bankId
     */
    public function setBankId($bankId): void
    {
        $this->bankId = $bankId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return bool|null
     */
    public function getEditable(): ?bool
    {
        return $this->editable;
    }

    /**
     * @param bool|null $editable
     */
    public function setEditable(?bool $editable): void
    {
        $this->editable = null === $editable ? false : $editable;
    }

    /**
     * @return string|null
     */
    public function getHtmlTemplate(): ?string
    {
        return $this->htmlTemplate;
    }

    /**
     * @param string|null $htmlTemplate
     */
    public function setHtmlTemplate(?string $htmlTemplate): void
    {
        $this->htmlTemplate = $htmlTemplate;
    }
}
