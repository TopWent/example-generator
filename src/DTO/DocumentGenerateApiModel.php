<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class DocumentGenerateApiModel
{
    /**
     * @var array $values Значения, подставляемые в шаблон
     * @Assert\NotBlank()
     */
    private $values;

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
     * @var integer|null $applicationId ID заявки
     * @Assert\Positive()
     */
    private $applicationId;

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
     * @return int|null
     */
    public function getApplicationId(): ?int
    {
        return $this->applicationId;
    }

    /**
     * @param int|null $applicationId
     */
    public function setApplicationId(?int $applicationId): void
    {
        $this->applicationId = $applicationId;
    }
}
