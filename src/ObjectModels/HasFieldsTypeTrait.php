<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\API\Schema\SchemaDefinition;
use PoP\GraphQL\Facades\Registries\FieldRegistryFacade;

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
        }
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
