<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class SpecialTemplateCreateApiModel
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
     * @Assert\NotBlank()
     */
    private $applicationId;

    /**
     * @var string $body Тело Html в base64
     * @Assert\NotBlank()
     */
    private $body;

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
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }
}
