<?php

namespace App\Entity;

use RuntimeException;

class DocumentAlias
{
    const CF_FINANCIAL_STATEMENT = 'cfFinancialStatement';
    const CF_APPLICATION_PARAMS = 'cfApplicationParams';
    const CF_QUESTIONARE = 'cfQuestionnaire';

    const ALIASES = [
        self::CF_FINANCIAL_STATEMENT,
        self::CF_APPLICATION_PARAMS,
        self::CF_QUESTIONARE,
    ];

    public static function isValid(string $alias)
    {
        if (!self::checkExistAlias($alias)) {
            throw new RuntimeException('Алиас не найден');
        }
    }

    public static function checkExistAlias(string $alias)
    {
        return in_array($alias, self::ALIASES, true);
    }
}
