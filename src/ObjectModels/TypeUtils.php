<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\AbstractType;

class TypeUtils
{
    public const ID_SEPARATOR = '|';

    public static function getID(string $kind, string $name) {
        return $kind.self::ID_SEPARATOR.$name;
    }
    public static function getKindAndName(string $id) {
        return explode(self::ID_SEPARATOR, $id);
    }
}
