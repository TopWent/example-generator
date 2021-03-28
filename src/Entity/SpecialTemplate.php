<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Специальный редактируемый шаблон, который может быть изменен для конкретной заявки
 *
 * @ORM\Entity(repositoryClass="App\Repository\SpecialTemplateRepository")
 * @ORM\Table(uniqueConstraints={
 *     @UniqueConstraint(name="application_template_unique", columns={"application_id", "template_id"})
 * })
 */
class SpecialTemplate
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"specialTemplate:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="text", options={"comment"="Тело HTML, закодированное в base64"})
     * @Groups({"specialTemplate:read"})
     */
    private $body;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Template")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"specialTemplate:read"})
     */
    private $template;

    /**
     * @ORM\Column(type="string", length=255, options={"comment"="md5 hash, взятый от body"})
     */
    private $hash;

    /**
     * @ORM\Column(type="integer", options={"comment"="ID заявки"})
     * @Groups({"specialTemplate:read"})
     */
    private $applicationId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getTemplate(): ?Template
    {
        return $this->template;
    }

    public function setTemplate(?Template $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getApplicationId(): ?int
    {
        return $this->applicationId;
    }

    public function setApplicationId(int $applicationId): self
    {
        $this->applicationId = $applicationId;

        return $this;
    }
}
