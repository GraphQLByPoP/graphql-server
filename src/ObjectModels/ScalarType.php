<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\AbstractType;
use PoP\GraphQL\ObjectModels\NonDocumentableTypeTrait;

class ScalarType extends AbstractType
{
    use NonDocumentableTypeTrait;

    public function getKind(): string
    {
        return TypeKinds::SCALAR;
    }
}
