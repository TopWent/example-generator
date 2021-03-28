<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Exception;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TemplateRepository")
 * @ORM\Table(uniqueConstraints={
 *     @UniqueConstraint(name="alias_bank_id_unique", columns={"alias", "bank_id"})
 * })
 */
class Template
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"template:read", "template:read-with-params", "specialTemplate:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, options={"comment"="Название-идентификатор шаблона"})
     * @Groups({"template:read", "template:read-with-params"})
     */
    private $alias;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"comment"="ID банка"})
     * @Groups({"template:read", "template:read-with-params"})
     */
    private $bankId;

    /**
     * @deprecated Устаревшее поле, нужно записывать содержимое файла в $fileBase64
     * @ORM\Column(type="string", nullable=true, length=255, options={"comment"="Путь к файлу шаблона"})
     */
    private $filePath;

    /**
     * @Assert\IsNull()
     * @ORM\Column(type="text", nullable=true, options={"comment"="Файл шаблона в формате base64"})
     */
    private $fileBase64;

    /**
     * @ORM\Column(type="string", length=255, options={"comment"="Название шаблона"})
     * @Groups({"template:read", "template:read-with-params"})
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TemplateParameter", mappedBy="template", orphanRemoval=true)
     * @Groups({"template:read-with-params"})
     */
    private $templateParameters;

    /**
     * @ORM\Column(type="boolean", options={"default": false, "comment"="Является ли шаблон редактируемым?"})
     * @Groups({"template:read", "template:read-with-params"})
     */
    private $editable;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"comment"="Название файла с редактируемым шаблоном"})
     */
    private $htmlTemplate;

    public function __construct()
    {
        $this->templateParameters = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function getBankId(): ?int
    {
        return $this->bankId;
    }

    public function setBankId(?int $bankId): self
    {
        $this->bankId = $bankId;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|TemplateParameter[]
     */
    public function getTemplateParameters(): Collection
    {
        return $this->templateParameters;
    }

    public function addTemplateParameter(TemplateParameter $templateParameter): self
    {
        if (!$this->templateParameters->contains($templateParameter)) {
            $this->templateParameters[] = $templateParameter;
            $templateParameter->setTemplate($this);
        }

        return $this;
    }

    public function removeTemplateParameter(TemplateParameter $templateParameter): self
    {
        if ($this->templateParameters->contains($templateParameter)) {
            $this->templateParameters->removeElement($templateParameter);
            // set the owning side to null (unless already changed)
            if ($templateParameter->getTemplate() === $this) {
                $templateParameter->setTemplate(null);
            }
        }

        return $this;
    }

    /**
     * @return array|TemplateParameter[]
     */
    public function getArrayOfAllParameters(): array
    {
        $result = [];
        foreach ($this->getTemplateParameters() as $templateParameter) {
            $templateParameter->addToArray($result);
        }

        return $result;
    }

    public function getEditable(): ?bool
    {
        return $this->editable;
    }

    public function setEditable(bool $editable): self
    {
        $this->editable = $editable;

        return $this;
    }

    public function getHtmlTemplate(): ?string
    {
        return $this->htmlTemplate;
    }

    public function setHtmlTemplate(?string $htmlTemplate): self
    {
        $this->htmlTemplate = $htmlTemplate;

        return $this;
    }

    public function getHtmlTemplatePath(): ?string
    {
        return null !== $this->htmlTemplate ?
            $_ENV['HTML_BASE_TEMPLATES_PATH'].$this->htmlTemplate :
            null;
    }

    /**
     * @return string
     *
     * @throws Exception
     */
    public function getHtmlFromPath(): string
    {
        if (null === $this->getHtmlTemplatePath()) {
            throw new Exception('Не найден html шаблона с id '.$this->getId());
        }

        return file_get_contents($this->getHtmlTemplatePath());
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getFile(): string
    {
        // если заполнено поле с файлом в base64, берем файл из него
        if (!empty($this->fileBase64)) {
            $file = base64_decode($this->fileBase64, true);
        } else {
            // если нет, то ищем файл по пути из поля filePath и отдаем его контент
            $file = file_get_contents($this->filePath);
        }

        if (false === $file) {
            throw new Exception('Не удалось получить файл шаблона с id '.$this->getId());
        }

        return $file;
    }

    public function setFileBase64(string $fileBase64): void
    {
        $this->fileBase64 = $fileBase64;
    }
}
