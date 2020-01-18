<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\Facades\Registries\SchemaDefinitionReferenceRegistryFacade;

abstract class AbstractSchemaDefinitionReferenceObject
{
    protected $id;
    protected $fullSchemaDefinition;
    protected $schemaDefinitionPath;
    protected $schemaDefinition;
    public function __construct(array &$fullSchemaDefinition, array $schemaDefinitionPath)
    {
        // Also save this variable to lazy initi new types in HasLazyTypeSchemaDefinitionReferenceTrait
        $this->fullSchemaDefinition = $fullSchemaDefinition;
        $this->schemaDefinitionPath = $schemaDefinitionPath;

        // Retrieve this element's schema definition by iterating down its path starting from the root of the full schema definition
        $schemaDefinitionPointer = &$fullSchemaDefinition;
        foreach ($schemaDefinitionPath as $pathLevel) {
            // // The "dependent" token doesn't advance the definitionPath, but still allows to generate a unique ID
            // if ($pathLevel == SchemaDefinitionHelpers::DEPENDENT_TOKEN) {
            //     continue;
            // }
            $schemaDefinitionPointer = &$schemaDefinitionPointer[$pathLevel];
        }
        $this->schemaDefinition = $schemaDefinitionPointer;

        // Register the object, and get back its ID
        $schemaDefinitionReferenceRegistry = SchemaDefinitionReferenceRegistryFacade::getInstance();
        $this->id = $schemaDefinitionReferenceRegistry->registerSchemaDefinitionReference($this);
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
