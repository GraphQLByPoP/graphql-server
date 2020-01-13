<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\API\Schema\SchemaDefinition;
use PoP\GraphQL\ObjectModels\AbstractType;
use PoP\GraphQL\ObjectModels\HasFieldsTypeTrait;
use PoP\GraphQL\ObjectModels\HasFieldsTypeInterface;
use PoP\GraphQL\Facades\Registries\InterfaceRegistryFacade;

class InterfaceType extends AbstractType implements HasFieldsTypeInterface
{
    use HasFieldsTypeTrait;

    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->initFields($name);
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
}
