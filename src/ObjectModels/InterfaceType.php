<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\AbstractType;
use PoP\GraphQL\ObjectModels\HasFieldsTypeTrait;
use PoP\GraphQL\ObjectModels\HasFieldsTypeInterface;
use PoP\GraphQL\ObjectModels\HasPossibleTypesTypeTrait;
use PoP\GraphQL\ObjectModels\HasPossibleTypesTypeInterface;

class InterfaceType extends AbstractType implements HasFieldsTypeInterface, HasPossibleTypesTypeInterface
{
    use HasFieldsTypeTrait, HasPossibleTypesTypeTrait;

    public function __construct(array &$fullSchemaDefinition, array $schemaDefinitionPath)
    {
        parent::__construct($fullSchemaDefinition, $schemaDefinitionPath);

        $this->initFields($fullSchemaDefinition, $schemaDefinitionPath);
        // $this->initPossibleTypes($fullSchemaDefinition, $schemaDefinitionPath);
    }

    public function getKind(): string
    {
        return TypeKinds::INTERFACE;
    }
}
