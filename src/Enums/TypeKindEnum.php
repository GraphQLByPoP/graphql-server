<?php

declare(strict_types=1);

namespace PoP\GraphQL\Enums;

use PoP\ComponentModel\Enums\AbstractEnum;
use PoP\GraphQL\ObjectModels\TypeKinds;

class TypeKindEnum extends AbstractEnum
{
    public const NAME = 'TypeKind';

    protected function getEnumName(): string
    {
        return self::NAME;
    }
    public function getValues(): array
    {
        return [
            TypeKinds::SCALAR,
            TypeKinds::OBJECT,
            TypeKinds::INTERFACE,
            TypeKinds::UNION,
            TypeKinds::ENUM,
            TypeKinds::INPUT_OBJECT,
            TypeKinds::LIST,
            TypeKinds::NON_NULL,
        ];
    }
}
