<?php

declare(strict_types=1);

namespace PoP\GraphQLServer\ObjectModels;

use PoP\GraphQLServer\ObjectModels\AbstractType;
use PoP\GraphQLServer\ObjectModels\HasFieldsTypeTrait;
use PoP\GraphQLServer\ObjectModels\HasFieldsTypeInterface;
use PoP\GraphQLServer\ObjectModels\HasInterfacesTypeTrait;
use PoP\GraphQLServer\ObjectModels\HasPossibleTypesTypeTrait;
use PoP\GraphQLServer\ObjectModels\HasInterfacesTypeInterface;
use PoP\GraphQLServer\ObjectModels\HasPossibleTypesTypeInterface;

class InterfaceType extends AbstractType implements HasFieldsTypeInterface, HasPossibleTypesTypeInterface, HasInterfacesTypeInterface
{
    use HasFieldsTypeTrait, HasPossibleTypesTypeTrait, HasInterfacesTypeTrait;

    public function __construct(array &$fullSchemaDefinition, array $schemaDefinitionPath, array $customDefinition = [])
    {
        parent::__construct($fullSchemaDefinition, $schemaDefinitionPath, $customDefinition);

        $this->initFields($fullSchemaDefinition, $schemaDefinitionPath, false, false);
        $this->initInterfaces($fullSchemaDefinition, $schemaDefinitionPath);
    }
    public function initializeTypeDependencies(): void
    {
        $this->initPossibleTypes();
        $this->initializeFieldTypeDependencies();
    }

    public function getKind(): string
    {
        return TypeKinds::INTERFACE;
    }
}
