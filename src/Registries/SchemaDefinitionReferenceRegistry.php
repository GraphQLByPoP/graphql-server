<?php
namespace PoP\GraphQL\Registries;

use PoP\GraphQL\ObjectModels\UnionType;
use PoP\GraphQL\ObjectModels\ObjectType;
use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\GraphQL\Schema\SchemaDefinition as GraphQLSchemaDefinition;
use PoP\API\Facades\SchemaDefinitionRegistryFacade;
use PoP\GraphQL\Schema\SchemaDefinitionHelpers;
use PoP\GraphQL\ObjectModels\AbstractSchemaDefinitionReferenceObject;
use PoP\GraphQL\Registries\SchemaDefinitionReferenceRegistryInterface;

class SchemaDefinitionReferenceRegistry implements SchemaDefinitionReferenceRegistryInterface {

    protected $fullSchemaDefinition;
    protected $fullSchemaDefinitionReferenceMap;
    protected $fullSchemaDefinitionReferenceDictionary;
    protected $dynamicTypes = [];

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

            // Get the schema definitions
            $schemaDefinitionRegistry = SchemaDefinitionRegistryFacade::getInstance();
            $this->fullSchemaDefinition = $schemaDefinitionRegistry->getSchemaDefinition($fieldArgs);

            // Expand the full schema with more data that is needed for GraphQL
            // Add the scalar types
            $scalarTypeNames = [
                // GraphQLSchemaDefinition::TYPE_UNRESOLVED_ID,
                GraphQLSchemaDefinition::TYPE_ID,
                GraphQLSchemaDefinition::TYPE_STRING,
                GraphQLSchemaDefinition::TYPE_INT,
                GraphQLSchemaDefinition::TYPE_FLOAT,
                GraphQLSchemaDefinition::TYPE_BOOL,
                GraphQLSchemaDefinition::TYPE_OBJECT,
                GraphQLSchemaDefinition::TYPE_MIXED,
                GraphQLSchemaDefinition::TYPE_DATE,
                GraphQLSchemaDefinition::TYPE_TIME,
                GraphQLSchemaDefinition::TYPE_URL,
                GraphQLSchemaDefinition::TYPE_EMAIL,
                GraphQLSchemaDefinition::TYPE_IP,
            ];
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

        // Dynamic types are stored so that the schema can add them to its "types" field
        if ($referenceObject->isDynamicType()) {
            $this->dynamicTypes[] = $referenceObject;
        }
        return $referenceObjectID;
    }
    public function getSchemaDefinitionReference(
        string $referenceObjectID
    ): AbstractSchemaDefinitionReferenceObject
    {
        return $this->fullSchemaDefinitionReferenceDictionary[$referenceObjectID];
    }

    public function getDynamicTypes(): array
    {
        return $this->dynamicTypes;
    }
}
