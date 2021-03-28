<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\SpecialTemplateCreateApiModel;
use App\DTO\SpecialTemplateShowApiModel;
use App\Entity\SpecialTemplate;
use App\Entity\Template;
use App\Helper\ValidationHelper;
use App\Http\ApiResponse;
use App\Repository\SpecialTemplateRepository;
use App\Repository\TemplateRepository;
use App\Service\SpecialTemplateManager\CreatorInterface;
use App\Service\SpecialTemplateManager\PresenterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/v1")
 */
class SpecialTemplateController extends AbstractController
{
    use ValidationHelper;

    /**
     * @var TemplateRepository
     */
    private $templateRepository;

    /**
     * @var SpecialTemplateRepository
     */
    private $specialTemplateRepository;

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
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CreatorInterface
     */
    private $creator;

    /**
     * SpecialTemplateController constructor.
     *
     * @param SerializerInterface       $serializer
     * @param NormalizerInterface       $normalizer
     * @param ValidatorInterface        $validator
     * @param EntityManagerInterface    $entityManager
     * @param TemplateRepository        $templateRepository
     * @param CreatorInterface          $creator
     * @param SpecialTemplateRepository $specialTemplateRepository
     */
    public function __construct(
        SerializerInterface $serializer,
        NormalizerInterface $normalizer,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        TemplateRepository $templateRepository,
        CreatorInterface $creator,
        SpecialTemplateRepository $specialTemplateRepository)
    {
        $this->normalizer = $normalizer;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
        $this->templateRepository = $templateRepository;
        $this->creator = $creator;
        $this->specialTemplateRepository = $specialTemplateRepository;
    }

    /**
     * @Route("/special-template", name="create_special_template", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws ExceptionInterface
     */
    public function createSpecialTemplate(Request $request)
    {
        /** @var SpecialTemplateCreateApiModel $requestData */
        $requestData = $this->serializer->deserialize(
            $request->getContent(),
            SpecialTemplateCreateApiModel::class,
            'json'
        );
        $this->validate($requestData);

        $specialTemplate = $this->creator->create($requestData);
        $specialTemplate->setBody(base64_decode($specialTemplate->getBody()));

        return ApiResponse::returnSuccess(
            $this->normalizer->normalize(
                $specialTemplate,
                Template::class,
                ['groups' => 'specialTemplate:read']
            ));
    }

    /**
     * @Route("/special-template/{id}", name="update_special_template", requirements={"id":"\d+"}, methods={"PUT"})
     *
     * @param SpecialTemplate $specialTemplate
     * @param Request         $request
     *
     * @return JsonResponse
     */
    public function updateSpecialTemplate(Request $request, SpecialTemplate $specialTemplate = null)
    {
        if (null === $specialTemplate) {
            return new ApiResponse('error', null, 404, 'Шаблон не найден.');
        }
        $requestData = json_decode($request->getContent(), true);
        if (!isset($requestData['body'])) {
            throw new HttpException(400, 'Необходимо указать html для обновления.');
        }
        $specialTemplate = $this->creator->update($specialTemplate, $requestData['body']);

        return ApiResponse::returnSuccess([
            'body' => base64_decode($specialTemplate->getBody()),
        ], 'Шаблон успешно сохранен.');
    }

    /**
     * @Route("/special-template", name="show_template", methods={"GET"})
     *
     * @param Request            $request
     * @param PresenterInterface $presenter
     *
     * @return JsonResponse
     */
    public function showTemplateForEdit(Request $request, PresenterInterface $presenter)
    {
        /** @var SpecialTemplateShowApiModel $dto */
        $dto = $this->serializer->deserialize(
            $request->getContent(),
            SpecialTemplateShowApiModel::class,
            'json'
        );
        $this->validate($dto);

        return ApiResponse::returnSuccess(
            $presenter->init($dto)->prepareHtml()
        );
    }
}
