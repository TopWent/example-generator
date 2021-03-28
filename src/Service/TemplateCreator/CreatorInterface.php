<?php

declare(strict_types=1);

namespace App\Service\TemplateCreator;

use App\DTO\TemplateCreateApiModel;
use App\Entity\Template;

interface CreatorInterface
{
    public function init(TemplateCreateApiModel $dto): CreatorInterface;

    public function create(): Template;
}
