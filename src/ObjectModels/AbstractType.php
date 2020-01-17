<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\GraphQL\ObjectModels\AbstractSchemaDefinitionReferenceObject;

abstract class AbstractType extends AbstractSchemaDefinitionReferenceObject
{
    // public function getID(): string
    // {
    //     return $this->getKind().TypeUtils::ID_SEPARATOR.parent::getID();
    // }
    abstract public function getKind(): string;

    public function getName(): string
    {
        // if ($this->schemaDefinition) {
            return $this->schemaDefinition[SchemaDefinition::ARGNAME_NAME];
        // }
        // return $this->getKind();
    }
    public function getDescription(): ?string
    {
        // if ($this->schemaDefinition) {
            return $this->schemaDefinition[SchemaDefinition::ARGNAME_DESCRIPTION];
        // }
        // return null;
    }
}
