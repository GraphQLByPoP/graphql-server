<?php
namespace PoP\GraphQL\Registries;

use PoP\ComponentModel\Facades\Instances\InstanceManagerFacade;

class TypeRegistry implements TypeRegistryInterface {

    protected $globalFields;
    protected $globalConnections;
    protected $globalDirectives;
    protected $registryNameClasses;
    protected $registryNameDefinitions;

    function setGlobalFields(array $fields): void
    {
        $this->globalFields = $fields;
    }
    function setGlobalConnections(array $connections): void
    {
        $this->globalConnections = $connections;
    }
    function setGlobalDirectives(array $directives): void
    {
        $this->globalDirectives = $directives;
    }
    function getGlobalFields(): array
    {
        return $this->globalFields;
    }
    function getGlobalConnections(): array
    {
        return $this->globalConnections;
    }
    function getGlobalDirectives(): array
    {
        return $this->globalDirectives;
    }

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
