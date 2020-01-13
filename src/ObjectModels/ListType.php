<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\AbstractNestableTypesType;

class ListType extends AbstractNestableTypesType
{
    public function getKind()
    {
        return TypeKinds::LIST;
    }
}
