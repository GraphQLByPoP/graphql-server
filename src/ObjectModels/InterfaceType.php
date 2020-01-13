<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\API\Schema\SchemaDefinition;
use PoP\GraphQL\ObjectModels\AbstractType;
use PoP\GraphQL\ObjectModels\HasFieldsTypeTrait;
use PoP\GraphQL\ObjectModels\HasFieldsTypeInterface;
use PoP\GraphQL\Facades\Registries\InterfaceRegistryFacade;
use PoP\GraphQL\ObjectModels\HasPossibleTypesTypeInterface;

class InterfaceType extends AbstractType implements HasFieldsTypeInterface, HasPossibleTypesTypeInterface
{
    use HasFieldsTypeTrait;

    protected $possibleTypes;

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

    public function getKind()
    {
        return TypeKinds::INTERFACE;
    }

    public function getTypeDefinition(string $name): array
    {
        $interfaceRegistry = InterfaceRegistryFacade::getInstance();
        return $interfaceRegistry->getInterfaceDefinition($name);
    }

    protected function initPossibleTypes(string $name): void
    {
        $typeDefinition = $this->getTypeDefinition($name);
        $this->possibleTypes = $typeDefinition[SchemaDefinition::ARGNAME_POSSIBLE_TYPES];
    }

    public function getPossibleTypes(): array
    {
        return $this->possibleTypes;
    }
}
