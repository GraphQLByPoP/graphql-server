<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\AbstractNestableTypesType;

class ListType extends AbstractNestableTypesType
{
    public function getKind(): string
    {
        return TypeKinds::LIST;
    }
}
