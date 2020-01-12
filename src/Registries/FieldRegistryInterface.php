<?php
namespace PoP\GraphQL\Registries;

use PoP\GraphQL\ObjectModels\AbstractType;

interface FieldRegistryInterface {
    function registerField(AbstractType $type, string $field, array $fieldDefinition): void;
    function getType(string $id): AbstractType;
    function getFieldDefinition(string $id): array;
}
