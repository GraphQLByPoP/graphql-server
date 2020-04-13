<?php

declare(strict_types=1);

namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\TypeKinds;
use PoP\GraphQL\ObjectModels\AbstractNestableType;

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
