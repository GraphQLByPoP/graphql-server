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
        $name = $this->schemaDefinition[SchemaDefinition::ARGNAME_NAME];
        // Enum and InputObject are dynamic types: their name is composed by their field and their kind
        if ($this->isDynamicType()) {
            $name .= '_'.$this->getKind();
        }
        return $name;
    }
    public function getDescription(): ?string
    {
        return $this->schemaDefinition[SchemaDefinition::ARGNAME_DESCRIPTION];
    }
}
