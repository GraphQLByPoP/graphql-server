<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\ComponentModel\Schema\SchemaDefinition;

trait NonDocumentableTypeTrait
{
    /**
     * Use the type to represent the type's name (eg: for a scalar it will be "bool", "string", etc)
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->schemaDefinition[SchemaDefinition::ARGNAME_TYPE];
    }
    /**
     * Always return null since the description will not apply to the return type
     * (eg: a scalar is used as the type from a field, and the description belongs to the field, not to the type)
     *
     * @return string
     */
    public function getDescription(): ?string
    {
        return null;
    }
}
