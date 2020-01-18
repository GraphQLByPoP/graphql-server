<?php
namespace PoP\GraphQL\Registries;

use PoP\GraphQL\ObjectModels\AbstractSchemaDefinitionReferenceObject;

interface SchemaDefinitionReferenceRegistryInterface {
    public function &getFullSchemaDefinition(): array;
    public function &getFullSchemaDefinitionReferenceMap(): array;
    public function registerSchemaDefinitionReference(
        AbstractSchemaDefinitionReferenceObject $referenceObject
    ): string;
    public function getSchemaDefinitionReference(
        string $referenceObjectID
    ): AbstractSchemaDefinitionReferenceObject;
}
