<?php
namespace PoP\GraphQL\ObjectModels;

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
}
