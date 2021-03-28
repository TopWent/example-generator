<?php

declare(strict_types=1);

namespace App\Service\TemplateCreator;

use App\DTO\TemplateUpdateApiModel;
use App\Entity\Template;

interface UpdaterInterface
{
    public function init(TemplateUpdateApiModel $dto, Template $template): UpdaterInterface;

    public function update(): Template;
}
