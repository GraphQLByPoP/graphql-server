<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\AbstractType;
use PoP\GraphQL\Facades\Registries\FieldRegistryFacade;

class FieldUtils
{
    public const ID_SEPARATOR = '|';

    public static function getID(AbstractType $type, string $name) {
        return $type->getName().self::ID_SEPARATOR.$name;
    }
    public static function getTypeAndField(string $id) {
        $components = explode(self::ID_SEPARATOR, $id);
        $field = $components[1];
        $fieldRegistry = FieldRegistryFacade::getInstance();
        $type = $fieldRegistry->getType($id);
        return [
            $type,
            $field
        ];
    }
}
