<?php
namespace PoP\GraphQL\Registries;

use PoP\GraphQL\ObjectModels\FieldUtils;
use PoP\GraphQL\ObjectModels\AbstractType;

class FieldRegistry implements FieldRegistryInterface {

    protected $fieldNameTypes;
    protected $fieldNameDefinitions;

    function registerField(AbstractType $type, string $field, array $fieldDefinition): void
    {
        $id = FieldUtils::getID($type, $field);
        $this->fieldNameTypes[$id] = $type;
        $this->fieldNameDefinitions[$id] = $fieldDefinition;
    }
    function getType(string $id): AbstractType
    {
        return $this->fieldNameTypes[$id];
    }
    function getFieldDefinition(string $id): array
    {
        return $this->fieldNameDefinitions[$id];
    }
}
