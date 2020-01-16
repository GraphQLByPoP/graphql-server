<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\AbstractType;

class InputObjectType extends AbstractType
{
    public function getKind(): string
    {
        return TypeKinds::INPUT_OBJECT;
    }
}
