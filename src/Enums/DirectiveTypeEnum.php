<?php

declare(strict_types=1);

namespace PoP\GraphQL\Enums;

use PoP\ComponentModel\Enums\AbstractEnum;
use PoP\ComponentModel\Directives\DirectiveTypes;

class DirectiveTypeEnum extends AbstractEnum
{
    public const NAME = 'DirectiveType';

    protected function getEnumName(): string
    {
        return self::NAME;
    }
    public function getValues(): array
    {
        return [
            DirectiveTypes::QUERY,
            DirectiveTypes::SCHEMA,
        ];
    }
}
