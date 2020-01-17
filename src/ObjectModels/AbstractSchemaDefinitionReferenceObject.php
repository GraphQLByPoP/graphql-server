<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\SchemaDefinition\SchemaDefinitionHelpers;
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

        // Calculate and set the ID. If this is a nested type, its wrapping type will already have been registered under this ID
        // Hence, register it under another one
        $schemaDefinitionReferenceRegistry = SchemaDefinitionReferenceRegistryFacade::getInstance();
        $id = SchemaDefinitionHelpers::getID($this->schemaDefinitionPath);
        while ($schemaDefinitionReferenceRegistry->hasSchemaDefinitionReference($id)) {
            // Append the ID with a distinctive token at the end
            $id .= '*';
        }
        $this->id = $id;

        // Register myself into the referenceMap, under my ID
        $schemaDefinitionReferenceRegistry->registerSchemaDefinitionReference(
            $this,
            $this->id
        );
    }

    public function getSchemaDefinition(): array
    {
        return $this->schemaDefinition;
    }

    public function getID(): string
    {
        return $this->id;
        // return $this->getObjectModelFamily().($this->schemaDefinitionPath ? implode(TypeUtils::PATH_SEPARATOR, $this->schemaDefinitionPath) : '');
        // return $this->getObjectModelFamily().SchemaDefinitionHelpers::getID($this->schemaDefinitionPath);
        // return SchemaDefinitionHelpers::getID($this->schemaDefinitionPath);
    }
    // public function getID(): string
    // {
    //     return $this->getObjectModelFamily().TypeUtils::ID_SEPARATOR.($this->schemaDefinitionPath ?? '');
    // }
    // public function getObjectModelFamily(): string
    // {
    //     // The name of the class, without the namespace
    //     $qualifiedClassName = get_called_class();//__CLASS__;
    //     if ($pos = strrpos($qualifiedClassName, '\\')) {
    //         return substr($qualifiedClassName, $pos + 1);
    //     }
    //     return $qualifiedClassName;
    // }
}
