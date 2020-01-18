<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\Syntax\SyntaxHelpers;
use PoP\GraphQL\ObjectModels\EnumType;
use PoP\ComponentModel\Schema\SchemaDefinition;
// use PoP\GraphQL\Facades\Registries\SchemaRegistryFacade;

class TypeUtils
{
    public const SCALAR_TYPES = [
        SchemaDefinition::TYPE_OBJECT,
        SchemaDefinition::TYPE_MIXED,
        SchemaDefinition::TYPE_STRING,
        SchemaDefinition::TYPE_INT,
        SchemaDefinition::TYPE_FLOAT,
        SchemaDefinition::TYPE_BOOL,
        SchemaDefinition::TYPE_DATE,
        SchemaDefinition::TYPE_TIME,
        SchemaDefinition::TYPE_URL,
        SchemaDefinition::TYPE_EMAIL,
        SchemaDefinition::TYPE_IP,
    ];

    public const ID_SEPARATOR = '|';
    public const PATH_SEPARATOR = '.';
    public static function composeSchemaDefinitionPath(string $parentSchemaDefinitionPath, array $relativePath)
    {
        $schemaDefinitionPath = $parentSchemaDefinitionPath;
        foreach ($relativePath as $pathLevel) {
            $schemaDefinitionPath .= self::ID_SEPARATOR.$pathLevel;
        }
        return $schemaDefinitionPath;
    }

    public static function getResolvableTypeID(string $kind, string $name) {
        return $kind.self::ID_SEPARATOR.$name;
    }
    public static function getNestableTypeID(string $kind, string $name) {
        return $kind.self::ID_SEPARATOR.$name;
    }
    public static function getEnumTypeID(string $kind, string $fieldID/*, string $enumName*/) {
        // Add $fieldID at the end!!! Because it itself also contains "|", and we can't control it
        return $kind./*self::ID_SEPARATOR.$enumName.*/self::ID_SEPARATOR.$fieldID;
    }
    public static function getEnumValueID(string $fieldID, string $value) {
        // Add $fieldID at the end!!! Because it itself also contains "|", and we can't control it
        return $value.self::ID_SEPARATOR.$fieldID;
    }
    public static function extractKindFromID(string $id) {
        // The kind is always the first element before "|", or the whole ID if it doesn't require any extra information
        $components = explode(self::ID_SEPARATOR, $id);
        return $components[0];
    }
    public static function extractNameFromID(string $id) {
        $components = explode(self::ID_SEPARATOR, $id);
        return $components[1];
    }
    public static function extractNestedTypesFromID(string $id) {
        $components = explode(self::ID_SEPARATOR, $id);
        return $components[1];
    }
    // public static function extractFieldIDAndEnumNameFromID(string $id) {
    //     // $fieldID also contains "|", hence recalculate it as everything else after the enumName
    //     $components = explode(self::ID_SEPARATOR, $id);
    //     $enumName = $components[1];
    //     $fieldID = substr($id, strlen($components[0])+strlen($components[1])+2*strlen(self::ID_SEPARATOR));
    //     return [
    //         $fieldID,
    //         $enumName
    //     ];
    // }
    public static function extractFieldIDFromID(string $id) {
        // $fieldID also contains "|", hence recalculate it as everything else after the enumName
        $components = explode(self::ID_SEPARATOR, $id);
        return substr($id, strlen($components[0])+strlen(self::ID_SEPARATOR));
    }
    public static function extractFieldIDAndEnumValueFromID(string $id) {
        // $fieldID also contains "|", hence recalculate it as everything else after the enumName
        $components = explode(self::ID_SEPARATOR, $id);
        $value = $components[0];
        $fieldID = substr($id, strlen($value)+strlen(self::ID_SEPARATOR));
        return [
            $fieldID,
            $value
        ];
    }

    public static function getTypeFromTypeName(string $typeName, array $schemaDefinitionPath): AbstractType
    {
        // Check if it is non-null
        if (SyntaxHelpers::isNonNullType($typeName)) {
            return new NonNullType(self::getTypeFromTypeName(SyntaxHelpers::getNonNullTypeNestedTypeName($typeName), $schemaDefinitionPath));
        }

        // Check if it is an array
        if (SyntaxHelpers::isListType($typeName)) {
            return new ListType(self::getTypeFromTypeName(SyntaxHelpers::getListTypeNestedTypeName($typeName), $schemaDefinitionPath));
        }

        // Check if it is an enum type
        if ($typeName == SchemaDefinition::TYPE_ENUM) {
            return new EnumType($schemaDefinitionPath);
        }

        // Check if it is any scalar
        if (in_array($typeName, self::SCALAR_TYPES)) {
            return new ScalarType($schemaDefinitionPath);
        }

        // Otherwise, it's either a Union or an Object. Find out from the TypeRegistry
        $schemaDefinition = self::getSchemaDefinitionByPath($schemaDefinitionPath);
        if ($schemaDefinition[SchemaDefinition::ARGNAME_IS_UNION]) {
            return new UnionType($schemaDefinitionPath);
        }
        return new ObjectType($schemaDefinitionPath);
    }

    // public static function &getSchemaDefinitionByPath(array $schemaDefinitionPath): array
    // {
    //     $schemaRegistry = SchemaRegistryFacade::getInstance();
    //     $schemaDefinitionPointer = $schemaRegistry->getFullSchemaDefinition();
    //     $schemaDefinitionPathLevels = explode(self::ID_SEPARATOR, $schemaDefinitionPath);
    //     foreach ($schemaDefinitionPathLevels as $pathLevel) {
    //         $schemaDefinitionPointer = &$schemaDefinitionPointer[$pathLevel];
    //     }
    //     return (array)$schemaDefinitionPointer;
    // }

    public static function getSchemaDefinitionPathFromID(string $id): string
    {
        $components = explode(self::ID_SEPARATOR, $id);
        return $components[count($components)-1];
    }
}
