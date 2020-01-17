<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\AbstractType;
use PoP\GraphQL\ObjectModels\HasPossibleTypesTypeTrait;
use PoP\GraphQL\ObjectModels\HasPossibleTypesTypeInterface;

class UnionType extends AbstractType implements HasPossibleTypesTypeInterface
{
    use HasPossibleTypesTypeTrait;

    // public function __construct(?string $schemaDefinitionPath = null)
    // {
    //     parent::__construct($schemaDefinitionPath);

    //     $this->initPossibleTypes();
    // }

    public function getKind(): string
    {
        return TypeKinds::UNION;
    }
}
