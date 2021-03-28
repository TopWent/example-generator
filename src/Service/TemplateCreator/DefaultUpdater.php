<?php

declare(strict_types=1);

namespace App\Service\TemplateCreator;

use App\DTO\TemplateUpdateApiModel;
use App\Entity\Template;
use App\Repository\TemplateParameterRepository;
use App\Repository\TemplateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class DefaultUpdater implements UpdaterInterface
{
    use ParamsCheckerTrait;
    /**
     * @var DocxFileHandler
     */
    private $fileHandler;

    /**
     * @var TemplateParamsCreator
     */
    private $paramsCreator;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TemplateRepository
     */
    private $templateRepository;

    /**
     * @var TemplateParameterRepository
     */
    private $parameterRepository;

    /**
     * @var Template
     */
    private $template;

    /**
     * @var TemplateUpdateApiModel
     */
    private $dto;

    /**
     * @var resource
     */
    private $tmpFile;

    public function __construct(
        DocxFileHandler $fileHandler,
        TemplateParamsCreator $paramsCreator,
        EntityManagerInterface $entityManager,
        TemplateRepository $templateRepository,
        TemplateParameterRepository $parameterRepository
    ) {
        $this->fileHandler = $fileHandler;
        $this->paramsCreator = $paramsCreator;
        $this->entityManager = $entityManager;
        $this->templateRepository = $templateRepository;
        $this->parameterRepository = $parameterRepository;
    }

    /**
     * @param TemplateUpdateApiModel $dto
     * @param Template               $template
     *
     * @return UpdaterInterface
     *
     * @throws \Exception
     */
    public function init(TemplateUpdateApiModel $dto, Template $template): UpdaterInterface
    {
        if ($this->isNotUniqueTemplate($template, $dto)) {
            throw new \Exception('Шаблон с таким alias и bankId уже существует.', Response::HTTP_BAD_REQUEST);
        }

        $this->dto = $dto;
        $this->template = $template;

        return $this;
    }

    /**
     * @return Template
     *
     * @throws \Exception
     */
    public function update(): Template
    {
        $this->entityManager->beginTransaction();
        try {
            if (null !== $this->dto->getFile()) {
                $this->template->setFileBase64($this->dto->getFile());
            }
            if (null !== $this->dto->getParams()) {
                $this->updateParameters();
            }

            $this->template
                ->setAlias($this->dto->getAlias() ?? $this->template->getAlias())
                ->setBankId($this->dto->getBankId() ?? $this->template->getBankId())
                ->setName($this->dto->getName() ?? $this->template->getName())
                ->setEditable($this->dto->getEditable() ?? $this->template->getEditable())
                ->setHtmlTemplate($this->dto->getHtmlTemplate() ?? $this->template->getHtmlTemplate());

            $this->entityManager->persist($this->template);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            $this->entityManager->rollback();

            throw new \Exception($exception->getMessage(), $exception->getCode());
        }
        $this->entityManager->commit();

        return $this->template;
    }

    private function initTemplateFile()
    {
        if (null === $this->tmpFile) {
            $this->tmpFile = tmpfile();
            $fileContent = null === $this->dto->getFile() ?
                $this->template->getFile() :
                base64_decode($this->dto->getFile());
            fwrite($this->tmpFile, $fileContent);

            $this->fileHandler->initFile($this->tmpFile);
        }
    }

    /**
     * При изменении алиаса или ID банка проверяет нет ли уже шаблона с такой уникальной связкой
     *
     * @param Template               $template
     * @param TemplateUpdateApiModel $dto
     *
     * @return bool
     */
    private function isNotUniqueTemplate(Template $template, TemplateUpdateApiModel $dto): bool
    {
        return ($template->getAlias() !== $dto->getAlias() || $template->getBankId() !== $dto->getBankId())
            && $this->templateRepository->findOneBy([
                'bankId' => $dto->getBankId(),
                'alias' => $dto->getAlias()
            ]) !== null;
    }

    /**
     * Обновляет параметры шаблона
     *
     * @throws \Exception
     */
    private function updateParameters()
    {
        $this->initTemplateFile();

        $this->parameterRepository->deleteAllParametersOfTemplate($this->template->getId());
        $this->paramsCreator
            ->init($this->template, $this->dto->getParams())
            ->addParamsToTemplate();
        $this->isMatchParamsAndPlaceholders();
    }

    public function __destruct()
    {
        fclose($this->tmpFile);
    }
}
