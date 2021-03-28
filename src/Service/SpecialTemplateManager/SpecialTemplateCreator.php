<?php

declare(strict_types=1);

namespace App\Service\SpecialTemplateManager;

use App\DTO\SpecialTemplateCreateApiModel;
use App\Entity\SpecialTemplate;
use App\Helper\HashGenerator;
use App\Repository\SpecialTemplateRepository;
use App\Repository\TemplateRepository;
use App\Service\DocumentGenerator\DefaultGenerator;
use App\Service\DocumentGenerator\GeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;

class SpecialTemplateCreator implements CreatorInterface
{
    /**
     * @var SpecialTemplateRepository
     */
    private $repository;

    /**
     * @var TemplateRepository
     */
    private $templateRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var DefaultGenerator
     */
    private $generator;

    public function __construct(
        SpecialTemplateRepository $repository,
        TemplateRepository $templateRepository,
        EntityManagerInterface $entityManager,
        GeneratorInterface $generator
    ) {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->templateRepository = $templateRepository;
        $this->generator = $generator;
    }

    /**
     * Сохраняет редактируемый шаблон или обновляет его, если он существует для данной заявки
     *
     * @param SpecialTemplateCreateApiModel $dto
     *
     * @return SpecialTemplate
     *
     * @throws \Exception
     */
    public function create(SpecialTemplateCreateApiModel $dto): SpecialTemplate
    {
        $template = $this->templateRepository->findOneBy([
            'bankId' => $dto->getBankId(),
            'alias' => $dto->getAlias(),
        ]);
        if (null === $template) {
            throw new \Exception('Исходный шаблон не найден.', 400);
        }

        $existedSpecialTemplate = $this->repository->findOneBy([
            'template' => $template->getId(),
            'applicationId' => $dto->getApplicationId(),
        ]);

        if (null !== $existedSpecialTemplate) {
            return $this->update($existedSpecialTemplate, $dto->getBody());
        }

        // заменяем в самом теле html инпуты на плейсхолдеры
        $html = base64_decode($dto->getBody());
        $html = $this->generator->setTemplate($template)->generateHtmlWithPlaceholders($html);

        $newSpecialTemplate = (new SpecialTemplate())
            ->setBody(base64_encode($html))
            ->setTemplate($template)
            ->setHash(HashGenerator::generate($dto->getBody()))
            ->setApplicationId($dto->getApplicationId());
        $this->entityManager->persist($newSpecialTemplate);
        $this->entityManager->flush();

        return $newSpecialTemplate;
    }

    /**
     * @param SpecialTemplate $specialTemplate
     * @param string          $newBodyBase64
     *
     * @return SpecialTemplate
     *
     * @throws \Exception
     */
    public function update(SpecialTemplate $specialTemplate, string $newBodyBase64): SpecialTemplate
    {
        $hash = HashGenerator::generate($newBodyBase64);
        if ($specialTemplate->getHash() === $hash) {
            throw new \Exception('Шаблон не был изменен.', 400);
        }

        // заменяем в самом теле html инпуты на плейсхолдеры
        $html = base64_decode($newBodyBase64);
        $html = $this->generator->setTemplate($specialTemplate->getTemplate())
            ->generateHtmlWithPlaceholders($html);

        $specialTemplate->setBody(base64_encode($html))->setHash($hash);
        $this->entityManager->persist($specialTemplate);
        $this->entityManager->flush();

        return $specialTemplate;
    }
}
