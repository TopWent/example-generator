<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TemplateParameterRepository")
 * @ORM\Table(uniqueConstraints={
 *     @UniqueConstraint(name="alias_template_unique", columns={"alias", "template_id", "parent_id"})
 * })
 */
class TemplateParameter
{
    public const TYPE_SIMPLE = 'simple';
    public const TYPE_ROW = 'row';
    public const TYPE_BLOCK = 'block';

    public const AVAILABLE_TYPES = [
        self::TYPE_SIMPLE,
        self::TYPE_ROW,
        self::TYPE_BLOCK,
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, options={"comment"="Название-идентификатор параметра"})
     * @Groups({"template:read-with-params"})
     */
    private $alias;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Template", inversedBy="templateParameters", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $template;

    /**
     * @Assert\Choice(choices=TemplateParameter::AVAILABLE_TYPES, message="Choose a valid type.")
     * @ORM\Column(type="string", length=50, options={"comment"="Тип параметра"})
     * @Groups({"template:read-with-params"})
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TemplateParameter", inversedBy="children", cascade={"persist"})
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TemplateParameter", mappedBy="parent")
     * @Groups({"template:read-with-params"})
     */
    private $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
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

    public function getTemplate(): ?Template
    {
        return $this->template;
    }

    public function setTemplate(?Template $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function addToArray(array &$result)
    {
        if (!empty($this->getChildren())) {
            foreach ($this->getChildren() as $child) {
                $child->addToArray($result);
            }
        }
        $result[$this->getAlias()] = $this;
    }
}
