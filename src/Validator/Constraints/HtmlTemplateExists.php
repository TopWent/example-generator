<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class HtmlTemplateExists extends Constraint
{
    public $message = 'The HTML template "{{ htmlTemplate }}" doesn\'t exists.';
}
