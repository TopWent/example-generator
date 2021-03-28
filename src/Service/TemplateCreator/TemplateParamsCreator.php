<?php

declare(strict_types=1);

namespace App\Service\TemplateCreator;

use App\Entity\Template;
use App\Entity\TemplateParameter;
use Doctrine\ORM\EntityManagerInterface;

class TemplateParamsCreator
{
    /**
     * @var Template
     */
    private $template;

    /**
     * @var array
     */
    private $params;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function init(Template $template, array $params): self
    {
        $this->template = $template;
        $this->params = $params;

        return $this;
    }

    public function addParamsToTemplate()
    {
        foreach ($this->params as $alias => $paramFields) {
            $this->template->addTemplateParameter(
                $this->handleParam($alias, $paramFields)
            );
        }
    }

    private function handleParam(string $alias, array $paramFields): TemplateParameter
    {
        $templateParameter = new TemplateParameter();
        $templateParameter->setAlias($alias)
            ->setTemplate($this->template)
            ->setType($paramFields['type']);

        if (isset($paramFields['children'])) {
            foreach ($paramFields['children'] as $childAlias => $childFields) {
                $templateParameter->addChild($this->handleParam($childAlias, $childFields));
            }
        }
        $this->entityManager->persist($templateParameter);

        return $templateParameter;
    }
}
