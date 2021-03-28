<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\TemplateCreateApiModel;
use App\DTO\TemplateUpdateApiModel;
use App\Entity\Template;
use App\Helper\ValidationHelper;
use App\Http\ApiResponse;
use App\Repository\TemplateRepository;
use App\Service\DocumentGenerator\GeneratorInterface;
use App\Service\TemplateCreator\DefaultCreator;
use App\Service\TemplateCreator\DefaultUpdater;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpWord\Exception\CopyFileException;
use PhpOffice\PhpWord\Exception\CreateTemporaryFileException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/v1")
 */
class TemplateController extends AbstractController
{
    use ValidationHelper;

    private const PER_PAGE_LIMIT = 50;
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
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * @var TemplateRepository
     */
    private $templateRepository;

    public function __construct(
        SerializerInterface $serializer,
        NormalizerInterface $normalizer,
        ValidatorInterface $validator,
        PaginatorInterface $paginator,
        TemplateRepository $templateRepository
    ) {
        $this->normalizer = $normalizer;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->paginator = $paginator;
        $this->templateRepository = $templateRepository;
    }

    /**
     * @Route("/template", name="create_template", methods={"POST"})
     *
     * @param Request        $request
     * @param DefaultCreator $creator
     *
     * @return JsonResponse
     *
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     * @throws ExceptionInterface
     */
    public function createTemplate(Request $request, DefaultCreator $creator): JsonResponse
    {
        /** @var TemplateCreateApiModel $dto */
        $dto = $this->serializer->deserialize(
            $request->getContent(),
            TemplateCreateApiModel::class,
            'json'
        );
        $this->validate($dto);

        $template = $creator->init($dto)->create();

        return ApiResponse::returnSuccess(
            $this->normalizer->normalize(
                $template,
                Template::class,
                ['groups' => 'template:read']
            ));
    }

    /**
     * @Route("/template/{id}", name="update_template", methods={"PUT"})
     *
     * @param Template       $template
     * @param Request        $request
     * @param DefaultUpdater $updater
     *
     * @return JsonResponse
     *
     * @throws CopyFileException
     * @throws CreateTemporaryFileException
     * @throws ExceptionInterface
     * @throws \Exception
     */
    public function updateTemplate(Request $request, DefaultUpdater $updater, Template $template = null): JsonResponse
    {
        if (null === $template) {
            return new ApiResponse('error', null, 404, 'Шаблон не найден.');
        }
        /** @var TemplateUpdateApiModel $dto */
        $dto = $this->serializer->deserialize(
            $request->getContent(),
            TemplateUpdateApiModel::class,
            'json'
        );
        $this->validate($dto);

        $template = $updater->init($dto, $template)->update();

        return ApiResponse::returnSuccess(
            $this->normalizer->normalize(
                $template,
                Template::class,
                ['groups' => 'template:read']
            ));
    }

    /**
     * @Route("/template", name="api_application_lot_list", methods={"GET"})
     *
     * @param Request $request
     *
     * @return ApiResponse
     *
     * @throws ExceptionInterface
     */
    public function getTemplateList(Request $request)
    {
        $requestData = json_decode($request->getContent(), true);
        $list = array_key_exists('bankId', $requestData ?? []) ?
            $this->templateRepository->findBy(['bankId' => $requestData['bankId']]) :
            $this->templateRepository->findAll();

        $paginator = $this->paginator->paginate(
            $list,
            $requestData['page'] ?? 1,
            self::PER_PAGE_LIMIT
        );

        $normData = $this->normalizer->normalize(
            $paginator->getItems(),
            Template::class,
            ['groups' => 'template:read-with-params']
        );

        return new ApiResponse(
            'success',
            [
                'items' => $normData,
                'totalCount' => $paginator->getTotalItemCount(),
            ]
        );
    }

    /**
     * @Route("/template/{id}", name="get_template", methods={"GET"})
     *
     * @param Template $template
     *
     * @return JsonResponse
     *
     * @throws ExceptionInterface
     */
    public function getTemplate(Template $template = null): JsonResponse
    {
        if (null === $template) {
            return new ApiResponse('error', null, 404, 'Шаблон не найден.');
        }

        return ApiResponse::returnSuccess(
            $this->normalizer->normalize(
                $template,
                Template::class,
                ['groups' => 'template:read']
            ));
    }

    /**
     * @Route("/template/file/{alias}/{bankId}", name="get_template_by_alias", methods={"GET"})
     *
     * @param GeneratorInterface $generator
     *
     * @param Template $template
     * @return Response
     */
    public function getTemplateByAlias(GeneratorInterface $generator, Template $template = null)
    {
        if (null === $template) {
            return new ApiResponse('error', null, 404, 'Шаблон не найден.');
        }

        $fileName = $generator->setTemplate($template)->generateTemplateFile();

        $response = new BinaryFileResponse($fileName);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $template->getName() . '.docx'
        );

        return $response;
    }

    /**
     * @Route("/template/{id}", name="delete_template", methods={"DELETE"})
     *
     * @param Template               $template
     * @param EntityManagerInterface $em
     *
     * @return JsonResponse
     */
    public function deleteTemplate(Template $template, EntityManagerInterface $em): JsonResponse
    {
        if (null === $template) {
            return new ApiResponse('error', null, 404, 'Шаблон не найден.');
        }
        $em->remove($template);
        $em->flush();

        return ApiResponse::returnSuccess(null, 'Успешно');
    }
}
