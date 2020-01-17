<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\API\Schema\SchemaDefinition;
use PoP\GraphQL\ObjectModels\InputValue;
use PoP\GraphQL\ObjectModels\HasLazyTypeSchemaDefinitionReferenceTrait;

class Field extends AbstractSchemaDefinitionReferenceObject
{
    use HasLazyTypeSchemaDefinitionReferenceTrait;

    protected $args;
    public function __construct(array &$fullSchemaDefinition, array $schemaDefinitionPath)
    {
        parent::__construct($fullSchemaDefinition, $schemaDefinitionPath);

        $this->initArgs($fullSchemaDefinition, $schemaDefinitionPath);
    }
    protected function initArgs(array &$fullSchemaDefinition, array $schemaDefinitionPath): void
    {
        $this->args = [];
        if ($args = $this->schemaDefinition[SchemaDefinition::ARGNAME_ARGS]) {
            foreach (array_keys($args) as $fieldArgName) {
                $fieldArgSchemaDefinitionPath = array_merge(
                    $schemaDefinitionPath,
                    [
                        SchemaDefinition::ARGNAME_ARGS,
                        $fieldArgName,
                    ]
                );
                $this->args[] = new InputValue(
                    $fullSchemaDefinition,
                    $fieldArgSchemaDefinitionPath
                );
            }
        }
    }
    public function getName(): string
    {
        return $this->schemaDefinition[SchemaDefinition::ARGNAME_NAME];
    }
    public function getDescription(): ?string
    {
        return $this->schemaDefinition[SchemaDefinition::ARGNAME_DESCRIPTION];
    }
    public function isDeprecated(): bool
    {
        return $this->schemaDefinition[SchemaDefinition::ARGNAME_DEPRECATED] ?? false;
    }
    public function getDeprecationDescription(): ?string
    {
        return $this->schemaDefinition[SchemaDefinition::ARGNAME_DEPRECATIONDESCRIPTION];
    }
    /**
     * Implementation of "args" field from the Field object (https://graphql.github.io/graphql-spec/draft/#sel-FAJbLACsEIDuEAA-vb)
     *
     * @return array of InputValue type
     */
    public function getArgs(): array
    {
        return $this->args;
    }
    public function getArgIDs(): array
    {
        return array_map(
            function(InputValue $inputValue) {
                return $inputValue->getID();
            },
            $this->getArgs()
        );
    }
}
