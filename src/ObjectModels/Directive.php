<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\GraphQL\ObjectModels\DirectiveLocations;
use PoP\GraphQL\ObjectModels\HasArgsSchemaDefinitionReferenceTrait;

class Directive extends AbstractSchemaDefinitionReferenceObject
{
    use HasArgsSchemaDefinitionReferenceTrait;

    public function __construct(array &$fullSchemaDefinition, array $schemaDefinitionPath, array $customDefinition = [])
    {
        parent::__construct($fullSchemaDefinition, $schemaDefinitionPath, $customDefinition);

        $this->initArgs($fullSchemaDefinition, $schemaDefinitionPath);
        $this->initializeArgsTypeDependencies();
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
        // They apply to the field (these are the same DirectiveLocations as used by "@skip": https://graphql.github.io/graphql-spec/draft/#sec--skip)
        return [
            DirectiveLocations::FIELD,
            DirectiveLocations::FRAGMENT_SPREAD,
            DirectiveLocations::INLINE_FRAGMENT,
        ];
    }
}
