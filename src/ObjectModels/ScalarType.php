<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\AbstractType;

class ScalarType extends AbstractType
{
    public function getKind()
    {
        return TypeKinds::SCALAR;
    }
}
