<?php
namespace PoP\GraphQL\Registries;

use PoP\GraphQL\ObjectModels\FieldUtils;
use PoP\GraphQL\ObjectModels\AbstractType;

class InputObjectRegistry implements InputObjectRegistryInterface {

    protected $inputObjectNameTypes;
    protected $inputObjectNameDefinitions;

    function registerInputObject(AbstractType $type, string $field, string $inputObjectName, array $fieldDefinition): void
    {
        // $id = FieldUtils::getInputObjectID($type, $field, $inputObjectName);
        // $this->inputObjectNameTypes[$id] = $type;
        // $this->inputObjectNameDefinitions[$id] = $fieldDefinition;
    }
    function getType(string $id): AbstractType
    {
        return $this->inputObjectNameTypes[$id];
    }
    function getInputObjectDefinition(string $id): array
    {
        return $this->inputObjectNameDefinitions[$id];
    }
}
