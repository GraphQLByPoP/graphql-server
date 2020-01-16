<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\Syntax\SyntaxHelpers;
use PoP\ComponentModel\Schema\SchemaDefinition;

class TypeUtils
{
    public const ID_SEPARATOR = '|';

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

    // public static function getTypeFromTypeName(string $typeName): AbstractType
    // {
    //     // Check if it is non-null
    //     if (SyntaxHelpers::isNonNullType($typeName)) {
    //         return new NonNullType(SyntaxHelpers::getNonNullTypeNestedTypes($typeName));
    //     }

    //     // Check if it is an array
    //     if (SyntaxHelpers::isListType($typeName)) {
    //         return new ListType(SyntaxHelpers::getListTypeNestedTypes($typeName));
    //     }

    //     // Check if it is an enum type
    //     if ($typeName == SchemaDefinition::TYPE_ENUM) {
    //         // $name = $this->fieldDefinition[SchemaDefinition::ARGNAME_NAME];
    //         return new EnumType($this->getID()/*, $name*/);
    //     }

    //     // Check if it is any scalar
    //     if (in_array($typeName, self::SCALAR_TYPES)) {
    //         return new ScalarType($typeName);
    //     }

    //     // Otherwise, it's either a Union or an Object. Find out from the TypeRegistry
    //     $typeRegistry = TypeRegistryFacade::getInstance();
    //     $typeDefinition = $typeRegistry->getTypeDefinition($typeName);
    //     if ($typeDefinition[SchemaDefinition::ARGNAME_IS_UNION]) {
    //         return new UnionType($typeName);
    //     }
    //     return new ObjectType($typeName);
    // }
}
