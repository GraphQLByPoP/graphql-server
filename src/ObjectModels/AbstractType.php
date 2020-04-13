<?php

declare(strict_types=1);

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

    public function getNamespacedName(): string
    {
        if ($this->isDynamicType()) {
            return $this->getName();
        }
        return $this->schemaDefinition[SchemaDefinition::ARGNAME_NAMESPACED_NAME];
    }

    public function getElementName(): string
    {
        if ($this->isDynamicType()) {
            return $this->getName();
        }
        return $this->schemaDefinition[SchemaDefinition::ARGNAME_ELEMENT_NAME];
    }

    public function getName(): string
    {
        // Enum and InputObject are dynamic types: their name is composed by their field and their kind
        // To make sure their names are unique, also include their full path
        // Otherwise, field of type "enum" with name "status" but under types "User" and "Post" would have the same name and collide
        if ($this->isDynamicType()) {
            return implode(
                '__', // Can't use '_', because it's reserved for the type/interface namespaces instead
                array_map(
                    'ucfirst',
                    $this->schemaDefinitionPath
                )
            );
        }
        // Static types: their names are defined under property "name"
        return $this->schemaDefinition[SchemaDefinition::ARGNAME_NAME];
    }
    public function getDescription(): ?string
    {
        return $this->schemaDefinition[SchemaDefinition::ARGNAME_DESCRIPTION];
    }
    /**
     * There are no extensions currently implemented for the Type
     *
     * @return array
     */
    public function getExtensions(): array
    {
        return [];
    }
}
