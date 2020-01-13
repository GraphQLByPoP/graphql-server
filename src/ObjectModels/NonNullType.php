<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\AbstractNestableTypesType;

class NonNullType extends AbstractNestableTypesType
{
    public function getKind()
    {
        return TypeKinds::NON_NULL;
    }
}
