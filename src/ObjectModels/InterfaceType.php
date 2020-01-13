<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\AbstractType;
use PoP\GraphQL\Facades\Registries\InterfaceRegistryFacade;

class InterfaceType extends AbstractType
{
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
