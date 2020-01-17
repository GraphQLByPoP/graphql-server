<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\TypeKinds;
use PoP\GraphQL\ObjectModels\AbstractNestableTypesType;

class NonNullType extends AbstractNestableTypesType
{
    use NonDocumentableTypeTrait;

    public function getKind(): string
    {
        return TypeKinds::NON_NULL;
    }
}
