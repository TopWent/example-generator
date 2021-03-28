<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Helper\ConverterConfigHandler;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Проверяет, что указанный редактируемый шаблон указан в конфигах и лежит в соответствующей папке.
 *
 * Class HtmlTemplateExistsValidator
 * @package App\Validator\Constraints
 */
class HtmlTemplateExistsValidator extends ConstraintValidator
{
    /**
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof HtmlTemplateExists) {
            throw new UnexpectedTypeException($constraint, HtmlTemplateExists::class);
        }
        if (null === $value) {
            return;
        }

        $filePath = $_ENV['HTML_BASE_TEMPLATES_PATH'].$value;

        if (!file_exists($filePath) || null === ConverterConfigHandler::getConverterType($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ htmlTemplate }}', $value)
                ->addViolation();
        }
    }
}
