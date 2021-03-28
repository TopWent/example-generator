<?php

declare(strict_types=1);

namespace App\Http;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse extends JsonResponse
{
    /**
     * ApiResponse constructor.
     *
     * @param string $statusResponse
     * @param string $message
     * @param null   $data
     * @param array  $errors
     * @param int    $status
     * @param array  $headers
     * @param bool   $json
     */
    public function __construct(string $statusResponse, $data = null, int $status = 200, string $message = '', array $errors = [], array $headers = [], bool $json = false)
    {
        parent::__construct($this->format($statusResponse, $data, $message, $errors), $status, $headers, $json);
    }

    public static function returnSuccess($data, string $message = ''): self
    {
        return new self('success', $data, 200, $message);
    }

    private function format(string $statusResponse, $data = null, string $message = '', array $errors = [])
    {
        if (null === $data) {
            $data = new \ArrayObject();
        }

        $response = [
            'status' => $statusResponse,
            'message' => $message,
            'data' => $data,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return $response;
    }
}
