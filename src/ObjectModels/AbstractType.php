<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\GraphQL\ObjectModels\AbstractSchemaDefinitionReferenceObject;

abstract class AbstractType extends AbstractSchemaDefinitionReferenceObject
{
    abstract public function getKind(): string;

    public function getName(): string
    {
        return $this->schemaDefinition[SchemaDefinition::ARGNAME_NAME];
    }
    public function getDescription(): ?string
    {
        return $this->schemaDefinition[SchemaDefinition::ARGNAME_DESCRIPTION];
    }
}
