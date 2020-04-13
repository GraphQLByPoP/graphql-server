<?php

declare(strict_types=1);

namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\AbstractType;
use PoP\GraphQL\ObjectModels\HasPossibleTypesTypeTrait;
use PoP\GraphQL\ObjectModels\HasPossibleTypesTypeInterface;

class UnionType extends AbstractType implements HasPossibleTypesTypeInterface
{
    use HasPossibleTypesTypeTrait;

    public function initializeTypeDependencies(): void
    {
        $this->initPossibleTypes();
    }

    public function getKind(): string
    {
        return TypeKinds::UNION;
    }
}
