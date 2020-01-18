<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\GraphQL\ObjectModels\DirectiveLocations;
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
    public function getLocations(): array
    {
        return [
            DirectiveLocations::QUERY,
            DirectiveLocations::MUTATION,
            DirectiveLocations::SUBSCRIPTION,
            DirectiveLocations::FIELD,
            DirectiveLocations::FRAGMENT_DEFINITION,
            DirectiveLocations::FRAGMENT_SPREAD,
            DirectiveLocations::INLINE_FRAGMENT,
            DirectiveLocations::SCHEMA,
            DirectiveLocations::SCALAR,
            DirectiveLocations::OBJECT,
            DirectiveLocations::FIELD_DEFINITION,
            DirectiveLocations::ARGUMENT_DEFINITION,
            DirectiveLocations::INTERFACE,
            DirectiveLocations::UNION,
            DirectiveLocations::ENUM,
            DirectiveLocations::ENUM_VALUE,
            DirectiveLocations::INPUT_OBJECT,
            DirectiveLocations::INPUT_FIELD_DEFINITION,
        ];
    }
}
