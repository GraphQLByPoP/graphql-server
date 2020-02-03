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
    /**
     * If an ObjectType implements an interface, and the interface implements the same field, then we must return the field definition as from the perspective of the interface!
     * Otherwise, when querying the schema in the GraphQL Playground (https://www.graphqlbin.com/v2/new/), it produces this error from a mismatched type:
     * "Error: ContentEntry.status expects type "Interfaces_ContentEntry_Fields_Status" but Post.status provides type "Types_Post_Fields_Status"."
     *
     * @param array $fullSchemaDefinition
     * @param array $interfaceNames
     * @return array
     */
    protected static function getFieldInterfaces(array &$fullSchemaDefinition, array $interfaceNames): array
    {
        $fieldInterfaces = [];
        if ($interfaceNames) {
            foreach ($interfaceNames as $interfaceName) {
                $interfaceSchemaDefinition = $fullSchemaDefinition[SchemaDefinition::ARGNAME_INTERFACES][$interfaceName];
                $interfaceFields = array_keys($interfaceSchemaDefinition[SchemaDefinition::ARGNAME_FIELDS]);
                // Watch out (again)! An interface can itself implement interfaces, and a field can be shared across them (eg: field "status" for interfaces ContentEntity and ContentEntry)
                // Then, check if the interface's interface also implements the field! Then, do not add it yet, leave it for its implemented interface to add it
                if ($interfaceImplementedInterfaceNames = $fullSchemaDefinition[SchemaDefinition::ARGNAME_INTERFACES][$interfaceName][SchemaDefinition::ARGNAME_INTERFACES]) {
                    $interfaceImplementedInterfaceFields = [];
                    foreach ($interfaceImplementedInterfaceNames as $interfaceImplementedInterfaceName) {
                        $interfaceImplementedInterfaceFields = array_merge(
                            $interfaceImplementedInterfaceFields,
                            array_keys($fullSchemaDefinition[SchemaDefinition::ARGNAME_INTERFACES][$interfaceImplementedInterfaceName][SchemaDefinition::ARGNAME_FIELDS])
                        );
                    }
                    $interfaceFields = array_diff(
                        $interfaceFields,
                        $interfaceImplementedInterfaceFields
                    );
                }
                // Set these fields as being defined by which interface
                foreach ($interfaceFields as $interfaceField) {
                    $fieldInterfaces[$interfaceField] = $interfaceName;
                }
            }
        }
        return $fieldInterfaces;
    }
    public static function initFieldsFromPath(array &$fullSchemaDefinition, array $fieldSchemaDefinitionPath, array $interfaceNames = []): array
    {
        $fieldInterfaces = self::getFieldInterfaces($fullSchemaDefinition, $interfaceNames);
        $fieldSchemaDefinitionPointer = self::advancePointerToPath($fullSchemaDefinition, $fieldSchemaDefinitionPath);
        $fields = [];
        foreach (array_keys($fieldSchemaDefinitionPointer) as $fieldName) {
            // If an ObjectType implements an interface, and the interface implements the same field, then we must return the field definition as from the perspective of the interface!
            if ($interfaceName = $fieldInterfaces[$fieldName]) {
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
    public static function retrieveFieldsFromPath(array &$fullSchemaDefinition, array $fieldSchemaDefinitionPath): array
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
