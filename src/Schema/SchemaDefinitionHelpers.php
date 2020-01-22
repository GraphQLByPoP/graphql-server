<?php
namespace PoP\GraphQL\Schema;

use PoP\GraphQL\ObjectModels\Field;
use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\GraphQL\Facades\Registries\SchemaDefinitionReferenceRegistryFacade;

class SchemaDefinitionHelpers
{
    public const PATH_SEPARATOR = '.';

    public static function getID(array $schemaDefinitionPath): string
    {
        return implode(
            self::PATH_SEPARATOR,
            $schemaDefinitionPath
        );
    }
    public static function &advancePointerToPath(array &$schemaDefinition, array $schemaDefinitionPath)
    {
        $schemaDefinitionPointer = &$schemaDefinition;
        foreach ($schemaDefinitionPath as $pathLevel) {
            $schemaDefinitionPointer = &$schemaDefinitionPointer[$pathLevel];
        }
        return $schemaDefinitionPointer;
    }
    public static function initFieldsFromPath(array &$fullSchemaDefinition, array $fieldSchemaDefinitionPath, array $interfaceNames): array
    {
        // Watch out! If an ObjectType implements an interface, and the interface implements the same field, then we must return the field definition as from the perspective of the interface!
        // Otherwise, when querying the schema in the GraphQL Playground (https://www.graphqlbin.com/v2/new/), it produces this error from a mismatched type:
        // "Error: ContentEntry.status expects type "Interfaces_ContentEntry_Fields_Status" but Post.status provides type "Types_Post_Fields_Status"."
        $fieldInterfaces = [];
        if ($interfaceNames) {
            foreach ($interfaceNames as $interfaceName) {
                $interfaceSchemaDefinition = $fullSchemaDefinition[SchemaDefinition::ARGNAME_INTERFACES][$interfaceName];
                foreach (array_keys($interfaceSchemaDefinition[SchemaDefinition::ARGNAME_FIELDS]) as $interfaceField) {
                    $fieldInterfaces[$interfaceField] = $interfaceName;
                }
            }
        }

        $fieldSchemaDefinitionPointer = self::advancePointerToPath($fullSchemaDefinition, $fieldSchemaDefinitionPath);
        $fields = [];
        foreach (array_keys($fieldSchemaDefinitionPointer) as $fieldName) {
            // If this field is covered by an interface, use the interface's definition of the field!
            if ($fieldInterfaces[$fieldName]) {
                $targetFieldSchemaDefinitionPath = [
                    SchemaDefinition::ARGNAME_INTERFACES,
                    $interfaceName,
                    SchemaDefinition::ARGNAME_FIELDS,
                ];
            } else {
                $targetFieldSchemaDefinitionPath = $fieldSchemaDefinitionPath;
            }
            $fields[] = new Field(
                $fullSchemaDefinition,
                array_merge(
                    $targetFieldSchemaDefinitionPath,
                    [
                        $fieldName
                    ]
                )
            );
        }
        return $fields;
    }
    public static function retrieveFieldsFromPath(array &$fullSchemaDefinition, array $fieldSchemaDefinitionPath, array $interfaceNames): array
    {
        $fieldSchemaDefinitionPointer = self::advancePointerToPath($fullSchemaDefinition, $fieldSchemaDefinitionPath);
        $schemaDefinitionReferenceRegistry = SchemaDefinitionReferenceRegistryFacade::getInstance();
        $fields = [];
        foreach (array_keys($fieldSchemaDefinitionPointer) as $fieldName) {
            $schemaDefinitionID = SchemaDefinitionHelpers::getID(array_merge(
                $fieldSchemaDefinitionPath,
                [
                    $fieldName
                ]
            ));
            $fields[] = $schemaDefinitionReferenceRegistry->getSchemaDefinitionReference($schemaDefinitionID);
        }
        return $fields;
    }
}
