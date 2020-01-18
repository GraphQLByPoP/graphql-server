<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\NonDocumentableTypeTrait;
use PoP\GraphQL\ObjectModels\AbstractNestableType;

class ListType extends AbstractNestableType
{
    use NonDocumentableTypeTrait;

    public function getKind(): string
    {
        return TypeKinds::LIST;
    }
}
