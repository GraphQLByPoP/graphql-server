<?php
namespace PoP\GraphQL\Registries;

use PoP\GraphQL\ObjectModels\AbstractSchemaDefinitionReferenceObject;

interface SchemaDefinitionReferenceRegistryInterface {
    public function &getFullSchemaDefinition(): array;
    public function &getFullSchemaDefinitionReferenceMap(): array;
    public function registerSchemaDefinitionReference(
        AbstractSchemaDefinitionReferenceObject $referenceObject,
        string $referenceObjectID
    ): void;
    public function getSchemaDefinitionReference(
        string $referenceObjectID
    ): AbstractSchemaDefinitionReferenceObject;
    public function hasSchemaDefinitionReference(
        string $referenceObjectID
    ): bool;
}
