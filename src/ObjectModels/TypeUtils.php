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
    public static function getEnumTypeID(string $kind, array $enumValues) {
        return $kind.self::ID_SEPARATOR.serialize($enumValues);
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
    public static function extractEnumValuesFromID(string $id) {
        $components = explode(self::ID_SEPARATOR, $id);
        return unserialize($components[1]);
    }
}
