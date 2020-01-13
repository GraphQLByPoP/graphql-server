<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\API\Schema\SchemaDefinition;
use PoP\GraphQL\ObjectModels\AbstractResolvableType;
use PoP\GraphQL\ObjectModels\HasFieldsTypeTrait;
use PoP\GraphQL\ObjectModels\HasFieldsTypeInterface;
use PoP\GraphQL\ObjectModels\HasPossibleTypesTypeTrait;
use PoP\GraphQL\Facades\Registries\InterfaceRegistryFacade;
use PoP\GraphQL\ObjectModels\HasPossibleTypesTypeInterface;

class InterfaceType extends AbstractResolvableType implements HasFieldsTypeInterface, HasPossibleTypesTypeInterface
{
    use HasFieldsTypeTrait, HasPossibleTypesTypeTrait;

    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->initFields($name);
        $this->initPossibleTypes($name);
    }

    protected function getFieldDefinitions(string $name)
    {
        $typeDefinition = $this->getTypeDefinition($name);
        return $typeDefinition[SchemaDefinition::ARGNAME_FIELDS];
    }

    public function getKind(): string
    {
        return TypeKinds::INTERFACE;
    }

    public function getTypeDefinition(string $name): array
    {
        $interfaceRegistry = InterfaceRegistryFacade::getInstance();
        return $interfaceRegistry->getInterfaceDefinition($name);
    }
}
