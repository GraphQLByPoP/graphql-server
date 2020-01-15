<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\AbstractType;
use PoP\GraphQL\Facades\Registries\FieldRegistryFacade;

class FieldUtils
{
    public const ID_SEPARATOR = '|';

    public static function getID(AbstractType $type, string $field) {
        return $field.self::ID_SEPARATOR.$type->getID();
    }
    public static function getInputObjectID(AbstractType $type, string $field, string $inputObjectName) {
        return $inputObjectName.self::ID_SEPARATOR.$field.self::ID_SEPARATOR.$type->getID();
    }
    public static function getTypeAndField(string $id) {
        $components = explode(self::ID_SEPARATOR, $id);
        $field = $components[0];
        $fieldRegistry = FieldRegistryFacade::getInstance();
        $type = $fieldRegistry->getType($id);
        return [
            $type,
            $field
        ];
    }
    public static function getTypeAndFieldAndFieldAndInputObjectName(string $id) {
        $components = explode(self::ID_SEPARATOR, $id);
        $inputObjectName = $components[0];
        $field = $components[1];
        $fieldRegistry = FieldRegistryFacade::getInstance();
        $type = $fieldRegistry->getType($id);
        return [
            $type,
            $field,
            $inputObjectName
        ];
    }
}
