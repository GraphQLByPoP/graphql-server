<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\GraphQL\ObjectModels\HasArgsSchemaDefinitionReferenceTrait;

class Directive extends AbstractSchemaDefinitionReferenceObject
{
    use HasArgsSchemaDefinitionReferenceTrait;

    public function __construct(array &$fullSchemaDefinition, array $schemaDefinitionPath)
    {
        parent::__construct($fullSchemaDefinition, $schemaDefinitionPath);

        $this->initArgs($fullSchemaDefinition, $schemaDefinitionPath);
    }
    public function getName(): string
    {
        return $this->schemaDefinition[SchemaDefinition::ARGNAME_NAME];
    }
    public function getDescription(): ?string
    {
        return $this->schemaDefinition[SchemaDefinition::ARGNAME_DESCRIPTION];
    }
}
