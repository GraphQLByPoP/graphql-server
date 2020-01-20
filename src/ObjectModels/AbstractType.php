<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\GraphQL\ObjectModels\AbstractSchemaDefinitionReferenceObject;

abstract class AbstractType extends AbstractSchemaDefinitionReferenceObject
{
    abstract public function getKind(): string;

    /**
     * Once all types are initialized, call this function to further link to other types
     *
     * @return void
     */
    public function initializeTypeDependencies(): void
    {

    }

    public function getName(): string
    {
        // Enum and InputObject are dynamic types: their name is composed by their field and their kind
        // To make sure their names are unique, also include their full path
        // Otherwise, field of type "enum" with name "status" but under types "User" and "Post" would have the same name and collide
        if ($this->isDynamicType()) {
            return implode('_', array_map('ucfirst', $this->schemaDefinitionPath));
        }
        // Static types: their names are defined under property "name"
        return $this->schemaDefinition[SchemaDefinition::ARGNAME_NAME];
    }
    public function getDescription(): ?string
    {
        return $this->schemaDefinition[SchemaDefinition::ARGNAME_DESCRIPTION];
    }
}
