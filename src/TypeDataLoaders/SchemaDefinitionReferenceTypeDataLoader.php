<?php
namespace PoP\GraphQL\TypeDataLoaders;

use PoP\ComponentModel\TypeDataLoaders\AbstractTypeDataLoader;
use PoP\GraphQL\Facades\Registries\SchemaDefinitionReferenceRegistryFacade;

class SchemaDefinitionReferenceTypeDataLoader extends AbstractTypeDataLoader
{
    public function getObjects(array $ids): array
    {
        $schemaDefinitionReferenceRegistry = SchemaDefinitionReferenceRegistryFacade::getInstance();
        return array_map(
            function($schemaDefinitionID) use($schemaDefinitionReferenceRegistry) {
                return $schemaDefinitionReferenceRegistry->getSchemaDefinitionReference($schemaDefinitionID);
            },
            $ids
        );
    }
}
