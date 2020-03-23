<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\InputValue;
use PoP\GraphQL\ObjectModels\AbstractType;
use PoP\ComponentModel\Schema\SchemaDefinition;

class InputObjectType extends AbstractType
{
    protected $inputValues;

    public function __construct(array &$fullSchemaDefinition, array $schemaDefinitionPath, array $customDefinition = [])
    {
        parent::__construct($fullSchemaDefinition, $schemaDefinitionPath, $customDefinition);

        $this->initInputValues($fullSchemaDefinition, $schemaDefinitionPath);
        foreach ($this->inputValues as $inputValue) {
            $inputValue->initializeTypeDependencies();
        }
    }
    protected function initInputValues(array &$fullSchemaDefinition, array $schemaDefinitionPath): void
    {
        $this->inputValues = [];
        if ($inputValues = $this->schemaDefinition[SchemaDefinition::ARGNAME_ARGS]) {
            foreach (array_keys($inputValues) as $inputValueName) {
                $inputValueSchemaDefinitionPath = array_merge(
                    $schemaDefinitionPath,
                    [
                        SchemaDefinition::ARGNAME_ARGS,
                        $inputValueName,
                    ]
                );
                $this->inputValues[] = new InputValue(
                    $fullSchemaDefinition,
                    $inputValueSchemaDefinitionPath
                );
            }
        }
    }

    public function isDynamicType(): bool
    {
        return true;
    }
    public function getKind(): string
    {
        return TypeKinds::INPUT_OBJECT;
    }
    public function getInputFields(): array
    {
        return $this->inputValues;

    }
    public function getInputFieldIDs(): array
    {
        return array_map(
            function(InputValue $inputValue) {
                return $inputValue->getID();
            },
            $this->getInputFields()
        );
    }
}
