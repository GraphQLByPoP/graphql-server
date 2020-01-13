<?php
namespace PoP\GraphQL\Registries;

// use PoP\GraphQL\ObjectModels\AbstractType;

interface InterfaceRegistryInterface {
    // function registerInterface(AbstractType $type, string $interfaceName, array $interfaceDefinition): void;
    function registerType(string $interfaceName, string $interfaceResolverClass, array $typeDefinition): void;
    // function getType(string $id): AbstractType;
    function getInterfaceDefinition(string $interfaceName): array;
}
