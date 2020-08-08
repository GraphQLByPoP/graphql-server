<?php

declare(strict_types=1);

namespace PoP\GraphQLServer\ObjectModels;

use PoP\GraphQLServer\ObjectModels\AbstractType;
use PoP\GraphQLServer\ObjectModels\HasPossibleTypesTypeTrait;
use PoP\GraphQLServer\ObjectModels\HasPossibleTypesTypeInterface;

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
