<?php

declare(strict_types=1);

namespace PoP\GraphQLServer\ObjectModels;

use PoP\GraphQLServer\ObjectModels\TypeKinds;
use PoP\GraphQLServer\ObjectModels\AbstractNestableType;

class NonNullType extends AbstractNestableType
{
    use NonDocumentableTypeTrait;

    public function getName(): string
    {
        return sprintf(
            '%s!',
            $this->nestedType->getName()
        );
    }

    public function getKind(): string
    {
        return TypeKinds::NON_NULL;
    }
}
