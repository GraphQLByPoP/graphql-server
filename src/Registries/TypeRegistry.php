<?php
namespace PoP\GraphQL\Registries;

use PoP\ComponentModel\Facades\Instances\InstanceManagerFacade;

class TypeRegistry implements TypeRegistryInterface {

    protected $registry;

    function registerType($name, $typeResolverClass): void
    {
        $this->registry[$name] = $typeResolverClass;
    }
    function getTypeResolverClass($name): string
    {
        return $this->registry[$name];
    }
    function getTypeResolverInstance($name): object
    {
        $instanceManager = InstanceManagerFacade::getInstance();
        $typeResolverClass = $this->getTypeResolverClass($name);
        return $instanceManager->getInstance($typeResolverClass);
    }
    function getTypeNames(): array
    {
        return array_keys($this->registry);
    }
    function getTypeResolverInstances(): array
    {
        return array_map(
            [$this, 'getTypeResolverInstance'],
            $this->getTypeNames()
        );
    }
}
