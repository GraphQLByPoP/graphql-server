<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\AbstractType;

abstract class AbstractNestableTypesType extends AbstractType
{
    protected $nestedTypes;
    public function __construct(string $nestedTypes)
    {
        $this->nestedTypes = $nestedTypes;
    }
    public function getID()
    {
        return TypeUtils::getNestableTypeID($this->getKind(), $this->nestedTypes);
    }
}
