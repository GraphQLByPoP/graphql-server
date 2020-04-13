<?php

declare(strict_types=1);

namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\AbstractType;
use PoP\GraphQL\ObjectModels\HasFieldsTypeTrait;
use PoP\GraphQL\ObjectModels\HasFieldsTypeInterface;
use PoP\GraphQL\ObjectModels\HasInterfacesTypeTrait;
use PoP\GraphQL\ObjectModels\HasPossibleTypesTypeTrait;
use PoP\GraphQL\ObjectModels\HasInterfacesTypeInterface;
use PoP\GraphQL\ObjectModels\HasPossibleTypesTypeInterface;

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
