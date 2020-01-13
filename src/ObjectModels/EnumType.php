<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\AbstractType;

class EnumType extends AbstractType
{
    // protected $enumValues;
    // function __construct(array $enumValues)
    // {
    //     $this->enumValues = $enumValues;
    // }
    public function getKind()
    {
        return TypeKinds::ENUM;
    }
}
