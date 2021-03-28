<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\DocumentGenerateApiModel;
use App\Helper\ValidationHelper;
use App\Http\ApiResponse;
use App\Repository\TemplateRepository;
use App\Service\DocumentGenerator\DefaultGenerator;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @Route("/api/v1")
 */
class GeneratorController extends AbstractController
{
    use ValidationHelper;

    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var ValidatorInterface;
     */
    private $validator;

    /**
     * @var TemplateRepository
     */
    private $templateRepository;

    public function __construct(
        SerializerInterface $serializer,
        NormalizerInterface $normalizer,
        ValidatorInterface $validator,
        TemplateRepository $templateRepository
    ) {
        $this->normalizer = $normalizer;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->templateRepository = $templateRepository;
    }

    /**
     * @Route("/generator/document-generate", name="generator", methods={"POST"})
     *
     * @param Request          $request
     * @param DefaultGenerator $generator
     *
     * @return JsonResponse
     *
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function generateDocument(Request $request, DefaultGenerator $generator): JsonResponse
    {
        /** @var DocumentGenerateApiModel $dto */
        $dto = $this->serializer->deserialize(
            $request->getContent(),
            DocumentGenerateApiModel::class,
            'json'
        );
        $this->validate($dto);

        $template = $this->templateRepository->findOneBy([
            'alias' => $dto->getAlias(),
            'bankId' => $dto->getBankId(),
        ]);

        if (null === $template) {
            throw new \Exception('Шаблон не найден.', 400);
        }

        return ApiResponse::returnSuccess([
            'fid' => $generator->setTemplate($template)
                ->generateDocxWithValues($dto->getValues(), $dto->getApplicationId()),
        ]);
    }
}
