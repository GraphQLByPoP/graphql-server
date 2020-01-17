<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\AbstractType;
use PoP\GraphQL\ObjectModels\HasFieldsTypeTrait;
use PoP\GraphQL\ObjectModels\HasFieldsTypeInterface;
use PoP\GraphQL\ObjectModels\HasInterfacesTypeTrait;
use PoP\GraphQL\ObjectModels\HasInterfacesTypeInterface;

class ObjectType extends AbstractType implements HasFieldsTypeInterface, HasInterfacesTypeInterface
{
    use HasFieldsTypeTrait, HasInterfacesTypeTrait;

    public function __construct(array &$fullSchemaDefinition, array $schemaDefinitionPath)
    {
        parent::__construct($fullSchemaDefinition, $schemaDefinitionPath);

        $this->initFields($fullSchemaDefinition, $schemaDefinitionPath);
        // $this->initInterfaces();
    }

    // protected function getFieldSchemaDefinitions()
    // {
    //     // Include the global fields and the ones specific to this type
    //     $schemaRegistry = SchemaRegistryFacade::getInstance();
    //     $fullSchemaDefinition = $schemaRegistry->getFullSchemaDefinition();
    //     return array_merge(
    //         $fullSchemaDefinition[SchemaDefinition::ARGNAME_GLOBAL_FIELDS],
    //         $fullSchemaDefinition[SchemaDefinition::ARGNAME_GLOBAL_CONNECTIONS],
    //         $this->schemaDefinition[SchemaDefinition::ARGNAME_FIELDS],
    //         // Connections can be null
    //         $this->schemaDefinition[SchemaDefinition::ARGNAME_CONNECTIONS] ?? []
    //     );
    // }

    public function getKind(): string
    {
        return TypeKinds::OBJECT;
    }
}
