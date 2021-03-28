<?php

declare(strict_types=1);

namespace App\Service\TemplateCreator;

use App\DTO\TemplateCreateApiModel;
use App\Entity\Template;
use App\Repository\TemplateRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;

class DefaultCreator implements CreatorInterface
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
     * @var TemplateCreateApiModel
     */
    private $dto;

    /**
     * @var Template
     */
    private $template;

    public function __construct(
        DocxFileHandler $fileHandler,
        TemplateParamsCreator $paramsCreator,
        EntityManagerInterface $entityManager,
        TemplateRepository $templateRepository)
    {
        $this->fileHandler = $fileHandler;
        $this->paramsCreator = $paramsCreator;
        $this->entityManager = $entityManager;
        $this->templateRepository = $templateRepository;
    }

    /**
     * @param TemplateCreateApiModel $dto
     *
     * @return CreatorInterface
     *
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     * @throws \Exception
     */
    public function init(TemplateCreateApiModel $dto): CreatorInterface
    {
        $this->dto = $dto;
        if ($this->doesTemplateExist($dto->getAlias(), $dto->getBankId())) {
            throw new \Exception('Такой шаблон уже существует.', 400);
        }

        $this->template = (new Template())
            ->setAlias($dto->getAlias())
            ->setBankId($dto->getBankId())
            ->setHtmlTemplate($dto->getHtmlTemplate())
            ->setEditable($dto->getEditable());
        $tempFile = tmpfile();
        fwrite($tempFile, base64_decode($dto->getFile()));

        $this->fileHandler->initFile($tempFile);
        fclose($tempFile);

        return $this;
    }

    /**
     * @return Template
     *
     * @throws \Exception
     */
    public function create(): Template
    {
        $this->template->setName($this->dto->getName());
        $this->paramsCreator
            ->init($this->template, $this->dto->getParams())
            ->addParamsToTemplate();

        $this->isMatchParamsAndPlaceholders();

        $this->template->setFileBase64($this->dto->getFile());
        $this->entityManager->persist($this->template);
        $this->entityManager->flush();

        return $this->template;
    }

    /**
     * @return string
     */
    private function generateFileName(): string
    {
        return $this->template->getAlias().$this->template->getBankId();
    }

    /**
     * @param string   $alias
     * @param int|null $bankId
     *
     * @return bool
     */
    private function doesTemplateExist(string $alias, ?int $bankId): bool
    {
        return null !== $this->templateRepository->findOneBy([
                'alias' => $alias,
                'bankId' => $bankId,
        ]);
    }
}
