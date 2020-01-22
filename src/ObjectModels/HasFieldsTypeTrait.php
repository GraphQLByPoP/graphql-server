<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\Environment;
use PoP\GraphQL\ObjectModels\Field;
use PoP\API\Schema\SchemaDefinition;
use PoP\GraphQL\Schema\SchemaDefinitionHelpers;

trait HasFieldsTypeTrait
{
    protected $fields;
    protected function initFields(array &$fullSchemaDefinition, array $schemaDefinitionPath, bool $includeConnections): void
    {
        $this->fields = [];
        $interfaceNames = $this->getInterfaceNames();

        // Iterate to the definition of the fields in the schema, and create an object for each of them
        // 1. Fields under this type
        $this->initFieldsFromPath(
            $fullSchemaDefinition,
            array_merge(
                $schemaDefinitionPath,
                [
                    SchemaDefinition::ARGNAME_FIELDS,
                ]
            ),
            $interfaceNames
        );
        // 2. Connections under this type
        if ($includeConnections) {
            $this->initFieldsFromPath(
                $fullSchemaDefinition,
                array_merge(
                    $schemaDefinitionPath,
                    [
                        SchemaDefinition::ARGNAME_CONNECTIONS,
                    ]
                ),
                $interfaceNames
            );
        }
        if (Environment::addGlobalFieldsToSchema()) {
            // Global fields and connections have already been initialized, simply get the reference to the existing objects from the registryMap
            // 1. Global fields
            $this->retrieveFieldsFromPath(
                $fullSchemaDefinition,
                [
                    SchemaDefinition::ARGNAME_GLOBAL_FIELDS,
                ]
            );
            // 2. Global connections
            if ($includeConnections) {
                $this->retrieveFieldsFromPath(
                    $fullSchemaDefinition,
                    [
                        SchemaDefinition::ARGNAME_GLOBAL_CONNECTIONS,
                    ]
                );
            }
        }
    }
    protected function getInterfaceNames()
    {
        if ($this instanceof HasInterfacesTypeInterface) {
            return $this->schemaDefinition[SchemaDefinition::ARGNAME_INTERFACES];
        }
        return [];
    }
    protected function initFieldsFromPath(array &$fullSchemaDefinition, array $fieldSchemaDefinitionPath, array $interfaceNames): void
    {
        $this->fields = array_merge(
            $this->fields,
            SchemaDefinitionHelpers::initFieldsFromPath($fullSchemaDefinition, $fieldSchemaDefinitionPath, $interfaceNames)
        );
    }
    protected function retrieveFieldsFromPath(array &$fullSchemaDefinition, array $fieldSchemaDefinitionPath): void
    {
        $this->fields = array_merge(
            $this->fields,
            SchemaDefinitionHelpers::retrieveFieldsFromPath($fullSchemaDefinition, $fieldSchemaDefinitionPath)
        );
    }
    public function initializeFieldTypeDependencies(): void
    {
        foreach ($this->fields as $field) {
            $field->initializeTypeDependencies();
        }
    }

    public function getFields(bool $includeDeprecated = false): array
    {
        return $includeDeprecated ?
            $this->fields :
            array_filter(
                $this->fields,
                function(Field $field) {
                    return !$field->isDeprecated();
                }
            );
    }
    public function getFieldIDs(bool $includeDeprecated = false): array
    {
        return array_map(
            function(Field $field) {
                return $field->getID();
            },
            $this->getFields($includeDeprecated)
        );
    }
}
