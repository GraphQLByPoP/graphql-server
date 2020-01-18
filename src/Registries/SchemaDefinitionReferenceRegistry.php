<?php
namespace PoP\GraphQL\Registries;

use PoP\GraphQL\ObjectModels\UnionType;
use PoP\GraphQL\ObjectModels\ObjectType;
use PoP\ComponentModel\Schema\SchemaHelpers;
use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\API\Facades\SchemaDefinitionRegistryFacade;
use PoP\GraphQL\SchemaDefinition\SchemaDefinitionHelpers;
use PoP\GraphQL\ObjectModels\AbstractSchemaDefinitionReferenceObject;
use PoP\GraphQL\Registries\SchemaDefinitionReferenceRegistryInterface;

class SchemaDefinitionReferenceRegistry implements SchemaDefinitionReferenceRegistryInterface {

    protected $fullSchemaDefinition;
    protected $fullSchemaDefinitionReferenceMap;
    protected $fullSchemaDefinitionReferenceDictionary;

    /**
     * It returns the full schema, expanded with all data required to satisfy GraphQL's introspection fields (starting from "__schema")
     *
     * @return array
     */
    public function &getFullSchemaDefinition(): array
    {
        if (is_null($this->fullSchemaDefinition)) {
            // These are the configuration options to work with the "full schema"
            $fieldArgs = [
                'deep' => true,
                'shape' => SchemaDefinition::ARGVALUE_SCHEMA_SHAPE_FLAT,
                'compressed' => true,
                'typeAsSDL' => true,
                'readable' => true,
            ];
            $options = [
                // 'use-type-resolver-class-as-schema-key' => true,
            ];

            // Get the schema definitions
            $schemaDefinitionRegistry = SchemaDefinitionRegistryFacade::getInstance();
            $this->fullSchemaDefinition = $schemaDefinitionRegistry->getSchemaDefinition($fieldArgs, $options);

            // Expand the full schema with more data that is needed for GraphQL
            // 1. Add the scalar types
            $scalarTypeNames = [
                // SchemaDefinition::TYPE_UNRESOLVED_ID,
                SchemaDefinition::TYPE_ID,
                SchemaDefinition::TYPE_STRING,
                SchemaDefinition::TYPE_INT,
                SchemaDefinition::TYPE_FLOAT,
                SchemaDefinition::TYPE_BOOL,
                SchemaDefinition::TYPE_ENUM,
                SchemaDefinition::TYPE_OBJECT,
                SchemaDefinition::TYPE_MIXED,
                SchemaDefinition::TYPE_DATE,
                SchemaDefinition::TYPE_TIME,
                SchemaDefinition::TYPE_URL,
                SchemaDefinition::TYPE_EMAIL,
                SchemaDefinition::TYPE_IP,
            ];
            $scalarTypeNames = array_map(
                function($scalarTypeName) {
                    return SchemaHelpers::convertTypeNameToGraphQLStandard($scalarTypeName);
                },
                $scalarTypeNames
            );
            foreach ($scalarTypeNames as $scalarTypeName) {
                $this->fullSchemaDefinition[SchemaDefinition::ARGNAME_TYPES][$scalarTypeName] = [
                    SchemaDefinition::ARGNAME_NAME => $scalarTypeName,
                    SchemaDefinition::ARGNAME_DESCRIPTION => null,
                    SchemaDefinition::ARGNAME_DIRECTIVES => null,
                    SchemaDefinition::ARGNAME_FIELDS => null,
                    SchemaDefinition::ARGNAME_CONNECTIONS => null,
                    SchemaDefinition::ARGNAME_INTERFACES => null,
                ];
            }

            // 2. Add the interfaces
        }

        return $this->fullSchemaDefinition;
    }

    public function &getFullSchemaDefinitionReferenceMap(): array
    {
        if (is_null($this->fullSchemaDefinitionReferenceMap)) {
            $fullSchemaDefinition = $this->getFullSchemaDefinition();
            $this->fullSchemaDefinitionReferenceMap = [];

            // Build the reference map from the schema definitions
            foreach ($fullSchemaDefinition[SchemaDefinition::ARGNAME_TYPES] as $typeName => $typeSchemaDefinition) {
                // $typeName = $typeSchemaDefinition[SchemaDefinition::ARGNAME_NAME];
                $typeSchemaDefinitionPath = [
                    SchemaDefinition::ARGNAME_TYPES,
                    $typeName
                ];
                // The type here can either be an ObjectType or a UnionType
                $typeInstance = $typeSchemaDefinition[SchemaDefinition::ARGNAME_IS_UNION] ?
                    new UnionType($fullSchemaDefinition, $typeSchemaDefinitionPath) :
                    new ObjectType($fullSchemaDefinition, $typeSchemaDefinitionPath);
                $this->fullSchemaDefinitionReferenceMap[SchemaDefinition::ARGNAME_TYPES][$typeName] = $typeInstance;
            }

            // // Register all the fields/directives/types in the TypeRegistry
            // $typeRegistry = TypeRegistryFacade::getInstance();
            // $typeRegistry->setGlobalFields(
            //     $this->fullSchema[SchemaDefinition::ARGNAME_GLOBAL_FIELDS]
            // );
            // $typeRegistry->setGlobalConnections(
            //     $this->fullSchema[SchemaDefinition::ARGNAME_GLOBAL_CONNECTIONS]
            // );
            // $typeRegistry->setGlobalDirectives(
            //     $this->fullSchema[SchemaDefinition::ARGNAME_GLOBAL_DIRECTIVES]
            // );
            // foreach ($this->fullSchema[SchemaDefinition::ARGNAME_TYPES] as $typeResolverClass => $typeDefinition) {
            //     $typeName = $typeDefinition[SchemaDefinition::ARGNAME_NAME];
            //     $typeRegistry->registerType($typeName, $typeResolverClass, $typeDefinition);
            // }
        }

        return $this->fullSchemaDefinitionReferenceMap;
    }
    public function registerSchemaDefinitionReference(
        AbstractSchemaDefinitionReferenceObject $referenceObject
    ): string
    {
        $schemaDefinitionPath = $referenceObject->getSchemaDefinitionPath();
        $referenceObjectID = SchemaDefinitionHelpers::getID($schemaDefinitionPath);
        // Calculate and set the ID. If this is a nested type, its wrapping type will already have been registered under this ID
        // Hence, register it under another one
        while (isset($this->fullSchemaDefinitionReferenceDictionary[$referenceObjectID])) {
            // Append the ID with a distinctive token at the end
            $referenceObjectID .= '*';
        }
        $this->fullSchemaDefinitionReferenceDictionary[$referenceObjectID] = $referenceObject;
        return $referenceObjectID;
    }
    public function getSchemaDefinitionReference(
        string $referenceObjectID
    ): AbstractSchemaDefinitionReferenceObject
    {
        return $this->fullSchemaDefinitionReferenceDictionary[$referenceObjectID];
    }
}
