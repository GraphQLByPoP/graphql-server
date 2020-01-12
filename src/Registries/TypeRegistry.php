<?php
namespace PoP\GraphQL\Registries;

use PoP\ComponentModel\Facades\Instances\InstanceManagerFacade;

class TypeRegistry implements TypeRegistryInterface {

    protected $registryNameClasses;
    protected $registryNameDefinitions;

    function registerType(string $name, string $typeResolverClass, array $typeDefinition): void
    {
        $this->registryNameClasses[$name] = $typeResolverClass;
        $this->registryNameDefinitions[$name] = $typeDefinition;
    }
    function getTypeResolverClass(string $name): string
    {
        return $this->registryNameClasses[$name];
    }
    function getTypeResolverInstance(string $name): object
    {
        $instanceManager = InstanceManagerFacade::getInstance();
        $typeResolverClass = $this->getTypeResolverClass($name);
        return $instanceManager->getInstance($typeResolverClass);
    }
    function getTypeDefinition(string $name): array
    {
        return $this->registryNameDefinitions[$name];
    }
    function getTypeNames(): array
    {
        return array_keys($this->registryNameClasses);
    }
    function getTypeResolverInstances(): array
    {
        return array_map(
            [$this, 'getTypeResolverInstance'],
            $this->getTypeNames()
        );
    }
}
