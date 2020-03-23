<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\Facades\Registries\SchemaDefinitionReferenceRegistryFacade;

abstract class AbstractSchemaDefinitionReferenceObject
{
    protected $id;
    protected $fullSchemaDefinition;
    protected $schemaDefinitionPath;
    protected $schemaDefinition;
    /**
     * Build a new Schema Definition Reference Object
     *
     * @param array $fullSchemaDefinition
     * @param array $schemaDefinitionPath
     * @param array $customDefinition Pass custom values that will override the ones defined in $schemaDefinition
     */
    public function __construct(array &$fullSchemaDefinition, array $schemaDefinitionPath, array $customDefinition = [])
    {
        // Also save this variable to lazy initi new types in HasTypeSchemaDefinitionReferenceTrait
        $this->fullSchemaDefinition = $fullSchemaDefinition;
        $this->schemaDefinitionPath = $schemaDefinitionPath;

        // Retrieve this element's schema definition by iterating down its path starting from the root of the full schema definition
        $schemaDefinitionPointer = &$fullSchemaDefinition;
        foreach ($schemaDefinitionPath as $pathLevel) {
            $schemaDefinitionPointer = &$schemaDefinitionPointer[$pathLevel];
        }
        $this->schemaDefinition = array_merge(
            $schemaDefinitionPointer,
            $customDefinition
        );

        // Register the object, and get back its ID
        $schemaDefinitionReferenceRegistry = SchemaDefinitionReferenceRegistryFacade::getInstance();
        $this->id = $schemaDefinitionReferenceRegistry->registerSchemaDefinitionReference($this);
    }

    public function isDynamicType(): bool
    {
        return false;
    }

    public function getSchemaDefinition(): array
    {
        return $this->schemaDefinition;
    }

    public function getSchemaDefinitionPath(): array
    {
        return $this->schemaDefinitionPath;
    }

    public function getID(): string
    {
        return $this->id;
    }
}
