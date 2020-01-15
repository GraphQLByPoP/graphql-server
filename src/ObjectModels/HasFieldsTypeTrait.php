<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\API\Schema\SchemaDefinition;
use PoP\GraphQL\Facades\Registries\FieldRegistryFacade;
use PoP\GraphQL\Facades\Registries\InputObjectRegistryFacade;

trait HasFieldsTypeTrait
{
    protected $fields;
    abstract protected function getFieldDefinitions(string $name);
    protected function initFields(string $name)
    {
        // Add the type as part of the ID, and register them in the fieldRegistry
        $fieldRegistry = FieldRegistryFacade::getInstance();
        $this->fields = [];
        $fieldDefinitions = $this->getFieldDefinitions($name);
        foreach ($fieldDefinitions as $field => $fieldDefinition) {
            $fieldRegistry->registerField($this, $field, $fieldDefinition);
            $this->fields[FieldUtils::getID($this, $field)] = $fieldDefinition;

            // If the field has arguments of type INPUT_OBJECT, also register them
            $this->initInputObjects($field, $fieldDefinition[SchemaDefinition::ARGNAME_ARGS]);
        }
    }

    protected function initInputObjects(string $field, ?array $inputObjectFieldArgs = null)
    {
        if (!$inputObjectFieldArgs) {
            return;
        }
        // if ($inputObjectFieldArgs = array_filter(
        //     $inputObjectFieldArgs,
        //     function($fieldArgDefinition) {
        //         if ($fieldArgDefinition[SchemaDefinition::ARGNAME_TYPE] == SchemaDefinition::TYPE_OBJECT) {
        //             return true;
        //         }
        //         return false;
        //     }
        // )) {
        $inputObjectRegistry = InputObjectRegistryFacade::getInstance();
        foreach ($inputObjectFieldArgs as $fieldArgName => $fieldArgDefinition) {
            $inputObjectRegistry->registerInputObject($this, $field, $fieldArgName, $fieldArgDefinition);
        }
        // }
    }

    public function getFields(bool $includeDeprecated = false): array
    {
        if ($includeDeprecated) {
            // Include all fields
            $fields = $this->fields;
        } else {
            // Filter out the deprecated fields
            $fields = array_filter(
                $this->fields,
                function($fieldDefinition) {
                    return !$fieldDefinition[SchemaDefinition::ARGNAME_DEPRECATED];
                }
            );
        }
        return array_keys($fields);
    }
}
