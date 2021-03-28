<?php

declare(strict_types=1);

namespace App\DTO;

use App\Validator\Constraints as CustomAssert;
use Symfony\Component\Validator\Constraints as Assert;

class TemplateUpdateApiModel
{
    /**
     * @var string $file Файл в формате base64
     */
    private $file;

    /**
     * @var string $name Понятное название файла, можно на русском
     */
    private $name;

    /**
     * @var array $params Массив с параметрами
     */
    private $params;

    /**
     * @var int $bankId ID банка. Может быть пустым для общих шаблонов.
     * @Assert\Type("integer")
     */
    private $bankId;

    /**
     * @var string $alias Название-идентификатор шаблона на латинице
     */
    private $alias;

    /**
     * @var bool $editable Признак редактируемости шаблона
     * @Assert\Type("boolean")
     */
    private $editable = false;

    /**
     * @var string $htmlTemplate Название файла с редактируемым шаблоном в формате html
     * @Assert\Type("string")
     * @CustomAssert\HtmlTemplateExists()
     */
    private $htmlTemplate;

    /**
     * @return string|null
     */
    public function getFile(): ?string
    {
        return $this->file;
    }

    /**
     * @param string|null $file
     */
    public function setFile(?string $file): void
    {
        $this->file = $file;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return array|null
     */
    public function getParams(): ?array
    {
        return $this->params;
    }

    /**
     * @param array|null $params
     */
    public function setParams(?array $params): void
    {
        $this->params = $params;
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
    public function setBankId(?int $bankId): void
    {
        $this->bankId = $bankId;
    }

    /**
     * @return string|null
     */
    public function getAlias(): ?string
    {
        return $this->alias;
    }

    /**
     * @param string|null $alias
     */
    public function setAlias(?string $alias): void
    {
        $this->alias = $alias;
    }
}
