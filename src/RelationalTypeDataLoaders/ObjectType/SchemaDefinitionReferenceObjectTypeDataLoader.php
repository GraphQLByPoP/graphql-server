<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer\RelationalTypeDataLoaders\ObjectType;

use GraphQLByPoP\GraphQLServer\Registries\SchemaDefinitionReferenceRegistryInterface;
use PoP\ComponentModel\RelationalTypeDataLoaders\ObjectType\AbstractObjectTypeDataLoader;

class SchemaDefinitionReferenceObjectTypeDataLoader extends AbstractObjectTypeDataLoader
{
    private ?SchemaDefinitionReferenceRegistryInterface $schemaDefinitionReferenceRegistry = null;

    final public function setSchemaDefinitionReferenceRegistry(SchemaDefinitionReferenceRegistryInterface $schemaDefinitionReferenceRegistry): void
    {
        $this->schemaDefinitionReferenceRegistry = $schemaDefinitionReferenceRegistry;
    }
    final protected function getSchemaDefinitionReferenceRegistry(): SchemaDefinitionReferenceRegistryInterface
    {
        /** @var SchemaDefinitionReferenceRegistryInterface */
        return $this->schemaDefinitionReferenceRegistry ??= $this->instanceManager->getInstance(SchemaDefinitionReferenceRegistryInterface::class);
    }

    /**
     * @param array<string|int> $ids
     * @return array<object|null>
     */
    public function getObjects(array $ids): array
    {
        /** @var string[] $ids */
        return array_map(
            $this->getSchemaDefinitionReferenceRegistry()->getSchemaDefinitionReferenceObject(...),
            $ids
        );
    }
}