<?php

declare(strict_types=1);

namespace App\Service\SpecialTemplateManager;

use App\DTO\SpecialTemplateShowApiModel;

interface PresenterInterface
{
    public function init(SpecialTemplateShowApiModel $dto): PresenterInterface;

    public function prepareHtml();
}
