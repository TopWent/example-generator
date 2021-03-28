<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class SpecialTemplateShowApiModel
{
    /**
     * @var string $alias Название-идентификатор шаблона
     * @Assert\NotBlank()
     */
    private $alias;

    /**
     * @var integer|null $bankId ID банка
     * @Assert\Positive()
     */
    private $bankId;

    /**
     * @var int $applicationId ID заявки
     * @Assert\Positive()
     * @Assert\NotNull()
     */
    private $applicationId;

    /**
     * @var array $values Значения плейсхолдеров в шаблоне
     * @Assert\NotBlank()
     */
    private $values;

    /**
     * @return string
     */
    public function getAlias(): string
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
     * @param int $templateId
     */
    public function setTemplateId(int $templateId): void
    {
        $this->templateId = $templateId;
    }

    /**
     * @return int
     */
    public function getApplicationId(): int
    {
        return $this->applicationId;
    }

    /**
     * @param int $applicationId
     */
    public function setApplicationId(int $applicationId): void
    {
        $this->applicationId = $applicationId;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param array $values
     */
    public function setValues(array $values): void
    {
        $this->values = $values;
    }
}
