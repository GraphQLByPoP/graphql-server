<?php
namespace PoP\GraphQL\Registries;

use PoP\GraphQL\ObjectModels\AbstractType;

interface InputObjectRegistryInterface {
    function registerInputObject(AbstractType $type, string $field, string $inputObjectName, array $fieldDefinition): void;
    function getType(string $id): AbstractType;
    function getInputObjectDefinition(string $id): array;
}
