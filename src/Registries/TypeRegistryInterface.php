<?php
namespace PoP\GraphQL\Registries;

interface TypeRegistryInterface {
    function setGlobalFields(array $fields): void;
    function setGlobalDirectives(array $directives): void;
    function getGlobalFields(): array;
    function getGlobalDirectives(): array;
    function registerType(string $name, string $typeResolverClass, array $typeDefinition): void;
    function getTypeResolverClass(string $name): string;
    function getTypeResolverInstance(string $name): object;
    function getTypeDefinition(string $name): array;
    function getTypeNames(): array;
    function getTypeResolverInstances(): array;
}
