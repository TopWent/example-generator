<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Http\ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getException();

        $response = $this->createApiResponse($exception);
        $event->setResponse($response);
    }

    private function createApiResponse(\Exception $exception): ApiResponse
    {
        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR;
        $errors = [];

        return new ApiResponse('error', null, $statusCode, $exception->getMessage(), $errors);
    }
}
