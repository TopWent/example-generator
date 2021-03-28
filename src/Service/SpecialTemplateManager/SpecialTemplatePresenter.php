<?php

declare(strict_types=1);

namespace App\Service\SpecialTemplateManager;

use App\DTO\SpecialTemplateShowApiModel;
use App\Repository\SpecialTemplateRepository;
use App\Repository\TemplateRepository;
use App\Service\DocumentGenerator\GeneratorInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SpecialTemplatePresenter implements PresenterInterface
{
    /**
     * @var SpecialTemplateShowApiModel
     */
    private $dto;

    /**
     * @var TemplateRepository
     */
    private $templateRepository;

    /**
     * @var SpecialTemplateRepository
     */
    private $specialTemplateRepository;

    /**
     * @var GeneratorInterface
     */
    private $generator;

    public function __construct(
        TemplateRepository $templateRepository,
        SpecialTemplateRepository $specialTemplateRepository,
        GeneratorInterface $generator
    ) {
        $this->templateRepository = $templateRepository;
        $this->specialTemplateRepository = $specialTemplateRepository;
        $this->generator = $generator;
    }

    /**
     * @param SpecialTemplateShowApiModel $dto
     *
     * @return PresenterInterface
     */
    public function init(SpecialTemplateShowApiModel $dto): PresenterInterface
    {
        $this->dto = $dto;

        return $this;
    }

    /**
     * Готовит html для того, чтобы показать на форнте.
     *
     * @return string
     *
     * @throws \Exception
     */
    public function prepareHtml(): string
    {
        $template = $this->templateRepository->findOneBy([
            'alias' => $this->dto->getAlias(),
            'bankId' => $this->dto->getBankId(),
        ]);

        if (null === $template) {
            throw new HttpException(404, 'Не найден шаблон.');
        }

        $specialTemplate = $this->specialTemplateRepository->findOneBy([
            'template' => $template->getId(),
            'applicationId' => $this->dto->getApplicationId(),
        ]);

        if (null === $specialTemplate) {
            $html = $template->getHtmlFromPath();
        } else {
            $html = base64_decode($specialTemplate->getBody());
        }

        return $this->generator
                ->setTemplate($template)
                ->generateHtmlWithInputs($html, $this->dto->getValues());
    }
}
