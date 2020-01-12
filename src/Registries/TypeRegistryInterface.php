<?php
namespace PoP\GraphQL\Registries;

interface TypeRegistryInterface {
    function registerType($name, $typeResolverClass): void;
    function getTypeResolverClass($name): string;
    function getTypeResolverInstance($name): object;
    function getTypeNames(): array;
    function getTypeResolverInstances(): array;
}
