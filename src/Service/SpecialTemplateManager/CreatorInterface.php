<?php

declare(strict_types=1);

namespace App\Service\SpecialTemplateManager;

use App\DTO\SpecialTemplateCreateApiModel;
use App\Entity\SpecialTemplate;

interface CreatorInterface
{
    public function create(SpecialTemplateCreateApiModel $dto): SpecialTemplate;

    public function update(SpecialTemplate $specialTemplate, string $newBodyBase64): SpecialTemplate;
}
