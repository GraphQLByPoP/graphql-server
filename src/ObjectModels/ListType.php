<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\NonDocumentableTypeTrait;
use PoP\GraphQL\ObjectModels\AbstractNestableTypesType;

class ListType extends AbstractNestableTypesType
{
    use NonDocumentableTypeTrait;

    public function getKind(): string
    {
        return TypeKinds::LIST;
    }
}
