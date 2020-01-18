<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\TypeKinds;
use PoP\GraphQL\ObjectModels\AbstractNestableType;

class NonNullType extends AbstractNestableType
{
    use NonDocumentableTypeTrait;

    public function getKind(): string
    {
        return TypeKinds::NON_NULL;
    }
}
