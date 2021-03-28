<?php

declare(strict_types=1);


namespace App\Helper;


use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

trait ValidationHelper
{
    /**
     * @param $objectForValidation object Объект для валидации
     */
    public function validate($objectForValidation)
    {
        /** @var ConstraintViolationListInterface $violations */
        $violations = $this->validator->validate($objectForValidation);
        if ($violations->count() > 0) {
            $errorMessage = '';
            /** @var ConstraintViolation $violation */
            foreach ($violations as $violation) {
                $errorMessage .= $violation->getPropertyPath() . ': ' . $violation->getMessage() . '; ';
            }

            throw new HttpException(400, $errorMessage);
        }
    }
}