<?php

declare(strict_types=1);

namespace App\Service\Converter\Exception;

use Symfony\Component\HttpFoundation\Response;

class IncorrectLinkException extends \Exception
{
    public function __construct(string $nodeValue)
    {
        $message = 'Ссылка "'.$nodeValue.'" является некорректной.';

        parent::__construct($message, Response::HTTP_BAD_REQUEST);
    }
}