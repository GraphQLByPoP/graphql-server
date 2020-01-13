<?php
namespace PoP\GraphQL\Registries;

// use PoP\GraphQL\ObjectModels\InterfaceType;

class InterfaceRegistry implements InterfaceRegistryInterface {

    protected $interfaceNameTypes;
    protected $interfaceNameDefinitions;

    function registerType(string $interfaceName, string $interfaceResolverClass, array $interfaceDefinition): void
    {
        $this->interfaceNameTypes[$interfaceName] = $interfaceResolverClass;
        $this->interfaceNameDefinitions[$interfaceName] = $interfaceDefinition;
    }
    // function getType(string $interface): InterfaceType
    // {
    //     return $this->interfaceNameTypes[$interface];
    // }
    function getInterfaceDefinition(string $interface): array
    {
        return $this->interfaceNameDefinitions[$interface];
    }
}
