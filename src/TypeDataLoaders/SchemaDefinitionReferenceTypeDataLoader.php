<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer\TypeDataLoaders;

use PoP\ComponentModel\TypeDataLoaders\AbstractTypeDataLoader;
use GraphQLByPoP\GraphQLServer\Facades\Registries\SchemaDefinitionReferenceRegistryFacade;

class SchemaDefinitionReferenceTypeDataLoader extends AbstractTypeDataLoader
{
    public function getObjects(array $ids): array
    {
        $schemaDefinitionReferenceRegistry = SchemaDefinitionReferenceRegistryFacade::getInstance();
        return array_map(
            function ($schemaDefinitionID) use ($schemaDefinitionReferenceRegistry) {
                return $schemaDefinitionReferenceRegistry->getSchemaDefinitionReference($schemaDefinitionID);
            },
            $ids
        );
    }
}
