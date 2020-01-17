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
        // // $schemaDefinitionReferenceMap = $schemaDefinitionReferenceRegistry->getFullSchemaDefinitionReferenceMap();
        // foreach ($ids as $id) {
        //     $schemaDefinitionPath = explode(TypeUtils::PATH_SEPARATOR, $id);
        //     $schemaDefinitionPointer = &$schemaDefinitionReferenceMap;
        //     foreach ($schemaDefinitionPath as $pathLevel) {
        //         $schemaDefinitionPointer = &$schemaDefinitionPointer[$pathLevel];
        //         // if (is_object($schemaDefinitionPointer)) {
        //         //     $schemaDefinitionPointer = &$schemaDefinitionPointer->$pathLevel;
        //         // } else {
        //         //     $schemaDefinitionPointer = &$schemaDefinitionPointer[$pathLevel];
        //         // }
        //     }
        //     // Add the element at which we arrived after iterating the path
        //     $ret[] = $schemaDefinitionPointer;
        // }
        // return $ret;
    }
}
