<?php
namespace PoP\GraphQL\Registries;

use PoP\GraphQL\Environment;
use PoP\GraphQL\Schema\SchemaHelpers;
use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\GraphQL\Schema\SchemaDefinitionHelpers;
use PoP\API\Facades\SchemaDefinitionRegistryFacade;
use PoP\Engine\DirectiveResolvers\ForEachDirectiveResolver;
use PoP\GraphQL\Facades\Schema\SchemaDefinitionServiceFacade;
use PoP\API\DirectiveResolvers\RenamePropertyDirectiveResolver;
use PoP\Engine\DirectiveResolvers\ApplyFunctionDirectiveResolver;
use PoP\API\DirectiveResolvers\DuplicatePropertyDirectiveResolver;
use PoP\GraphQL\Schema\SchemaDefinition as GraphQLSchemaDefinition;
use PoP\API\DirectiveResolvers\TransformArrayItemsDirectiveResolver;
use PoP\ComponentModel\DirectiveResolvers\ValidateDirectiveResolver;
use PoP\GraphQL\ObjectModels\AbstractSchemaDefinitionReferenceObject;
use PoP\API\DirectiveResolvers\CopyRelationalResultsDirectiveResolver;
use PoP\GraphQL\Registries\SchemaDefinitionReferenceRegistryInterface;
use PoP\Engine\DirectiveResolvers\SetSelfAsExpressionDirectiveResolver;
use PoP\Engine\DirectiveResolvers\AdvancePointerInArrayDirectiveResolver;
use PoP\API\DirectiveResolvers\SetPropertiesAsExpressionsDirectiveResolver;
use PoP\ComponentModel\DirectiveResolvers\ResolveValueAndMergeDirectiveResolver;

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
                'useTypeName' => true,
            ];

            // Get the schema definitions
            $schemaDefinitionRegistry = SchemaDefinitionRegistryFacade::getInstance();
            $this->fullSchemaDefinition = $schemaDefinitionRegistry->getSchemaDefinition($fieldArgs);

            // Convert the schema from PoP's format to what GraphQL needs to work with
            $schemaDefinitionService = SchemaDefinitionServiceFacade::getInstance();
            $queryTypeName = $schemaDefinitionService->getQueryTypeName();
            $this->prepareSchemaDefinitionForGraphQL($queryTypeName);
        }

        return $this->fullSchemaDefinition;
    }
    protected function prepareSchemaDefinitionForGraphQL(string $queryTypeName): void
    {
        // Remove the introspection fields that must not be added to the schema
        // Field "__typename" from all types (GraphQL spec @ https://graphql.github.io/graphql-spec/draft/#sel-FAJZHABFBKjrL):
        // "This field is implicit and does not appear in the fields list in any defined type."
        unset($this->fullSchemaDefinition[SchemaDefinition::ARGNAME_GLOBAL_FIELDS]['__typename']);

        // Fields "__schema" and "__type" from the query type (GraphQL spec @ https://graphql.github.io/graphql-spec/draft/#sel-FAJbHABABnD9ub):
        // "These fields are implicit and do not appear in the fields list in the root type of the query operation."
        unset($this->fullSchemaDefinition[SchemaDefinition::ARGNAME_TYPES][$queryTypeName][SchemaDefinition::ARGNAME_CONNECTIONS]['__type']);
        unset($this->fullSchemaDefinition[SchemaDefinition::ARGNAME_TYPES][$queryTypeName][SchemaDefinition::ARGNAME_CONNECTIONS]['__schema']);

        // Remove unneeded data
        if (!Environment::addGlobalFieldsToSchema()) {
            unset($this->fullSchemaDefinition[SchemaDefinition::ARGNAME_GLOBAL_FIELDS]);
            unset($this->fullSchemaDefinition[SchemaDefinition::ARGNAME_GLOBAL_CONNECTIONS]);
        }

        // Convert the field type from its internal representation (eg: "array:Post") to the GraphQL standard representation (eg: "[Post]")
        // 1. Global fields, connections and directives
        if (Environment::addGlobalFieldsToSchema()) {
            foreach (array_keys($this->fullSchemaDefinition[SchemaDefinition::ARGNAME_GLOBAL_FIELDS]) as $fieldName) {
                $this->introduceSDLNotationToFieldSchemaDefinition(
                    [
                        SchemaDefinition::ARGNAME_GLOBAL_FIELDS,
                        $fieldName
                    ]
                );
            }
            foreach (array_keys($this->fullSchemaDefinition[SchemaDefinition::ARGNAME_GLOBAL_CONNECTIONS]) as $connectionName) {
                $this->introduceSDLNotationToFieldSchemaDefinition(
                    [
                        SchemaDefinition::ARGNAME_GLOBAL_CONNECTIONS,
                        $connectionName
                    ]
                );
            }
        }
        // Directives: not all of them must be shown in the schema
        $hiddenDirectiveClasses = [];
        // Maybe remove the "system" directives
        if (!Environment::addSystemDirectivesToSchema()) {
            $hiddenDirectiveClasses = [
                ResolveValueAndMergeDirectiveResolver::class,
                ValidateDirectiveResolver::class,
            ];
        }
        // Maybe remove the Extended GraphQL directives
        if (!Environment::addExtendedGraphQLDirectivesToSchema()) {
            $hiddenDirectiveClasses = array_merge(
                $hiddenDirectiveClasses,
                [
                    AdvancePointerInArrayDirectiveResolver::class,
                    ApplyFunctionDirectiveResolver::class,
                    ForEachDirectiveResolver::class,
                    SetSelfAsExpressionDirectiveResolver::class,
                    CopyRelationalResultsDirectiveResolver::class,
                    DuplicatePropertyDirectiveResolver::class,
                    RenamePropertyDirectiveResolver::class,
                    SetPropertiesAsExpressionsDirectiveResolver::class,
                    TransformArrayItemsDirectiveResolver::class,
                ]
            );
        }
        foreach ($hiddenDirectiveClasses as $directiveClass) {
            unset($this->fullSchemaDefinition[SchemaDefinition::ARGNAME_GLOBAL_DIRECTIVES][$directiveClass::getDirectiveName()]);
        }
        // Add the directives
        foreach (array_keys($this->fullSchemaDefinition[SchemaDefinition::ARGNAME_GLOBAL_DIRECTIVES]) as $directiveName) {
            $this->introduceSDLNotationToFieldOrDirectiveArgs(
                [
                    SchemaDefinition::ARGNAME_GLOBAL_DIRECTIVES,
                    $directiveName
                ]
            );
        }
        // 2. Each type's fields, connections and directives
        foreach ($this->fullSchemaDefinition[SchemaDefinition::ARGNAME_TYPES] as $typeName => $typeSchemaDefinition) {
            foreach (array_keys($typeSchemaDefinition[SchemaDefinition::ARGNAME_FIELDS]) as $fieldName) {
                $this->introduceSDLNotationToFieldSchemaDefinition(
                    [
                        SchemaDefinition::ARGNAME_TYPES,
                        $typeName,
                        SchemaDefinition::ARGNAME_FIELDS,
                        $fieldName
                    ]
                );
            }
            foreach (array_keys($typeSchemaDefinition[SchemaDefinition::ARGNAME_CONNECTIONS]) as $connectionName) {
                $this->introduceSDLNotationToFieldSchemaDefinition(
                    [
                        SchemaDefinition::ARGNAME_TYPES,
                        $typeName,
                        SchemaDefinition::ARGNAME_CONNECTIONS,
                        $connectionName
                    ]
                );
            }
            foreach (array_keys($typeSchemaDefinition[SchemaDefinition::ARGNAME_DIRECTIVES]) as $directiveName) {
                $this->introduceSDLNotationToFieldOrDirectiveArgs(
                    [
                        SchemaDefinition::ARGNAME_TYPES,
                        $typeName,
                        SchemaDefinition::ARGNAME_DIRECTIVES,
                        $directiveName
                    ]
                );
            }
        }
        // 3. Interfaces
        foreach ($this->fullSchemaDefinition[SchemaDefinition::ARGNAME_INTERFACES] as $interfaceName => $interfaceSchemaDefinition) {
            foreach (array_keys($interfaceSchemaDefinition[SchemaDefinition::ARGNAME_FIELDS]) as $fieldName) {
                $this->introduceSDLNotationToFieldSchemaDefinition(
                    [
                        SchemaDefinition::ARGNAME_INTERFACES,
                        $interfaceName,
                        SchemaDefinition::ARGNAME_FIELDS,
                        $fieldName
                    ]
                );
            }
        }

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
    /**
     * Convert the field type from its internal representation (eg: "array:Post") to the GraphQL standard representation (eg: "[Post]")
     *
     * @param array $fieldSchemaDefinitionPath
     * @return void
     */
    protected function introduceSDLNotationToFieldSchemaDefinition(array $fieldSchemaDefinitionPath): void
    {
        $fieldSchemaDefinition = &SchemaDefinitionHelpers::advancePointerToPath($this->fullSchemaDefinition, $fieldSchemaDefinitionPath);
        if ($type = $fieldSchemaDefinition[SchemaDefinition::ARGNAME_TYPE]) {
            $fieldSchemaDefinition[SchemaDefinition::ARGNAME_TYPE] = SchemaHelpers::getFieldOrDirectiveArgTypeToOutputInSchema($type, $fieldSchemaDefinition[SchemaDefinition::ARGNAME_MANDATORY]);
        }
        $this->introduceSDLNotationToFieldOrDirectiveArgs($fieldSchemaDefinitionPath);
    }
    protected function introduceSDLNotationToFieldOrDirectiveArgs(array $fieldOrDirectiveSchemaDefinitionPath): void
    {
        $fieldOrDirectiveSchemaDefinition = &SchemaDefinitionHelpers::advancePointerToPath($this->fullSchemaDefinition, $fieldOrDirectiveSchemaDefinitionPath);

        // Also for the fieldOrDirective arguments
        if ($fieldOrDirectiveArgs = $fieldOrDirectiveSchemaDefinition[SchemaDefinition::ARGNAME_ARGS]) {
            foreach ($fieldOrDirectiveArgs as $fieldOrDirectiveArgName => $fieldOrDirectiveArgSchemaDefinition) {
                if ($type = $fieldOrDirectiveArgSchemaDefinition[SchemaDefinition::ARGNAME_TYPE]) {
                    $fieldOrDirectiveSchemaDefinition[SchemaDefinition::ARGNAME_ARGS][$fieldOrDirectiveArgName][SchemaDefinition::ARGNAME_TYPE] = SchemaHelpers::getFieldOrDirectiveArgTypeToOutputInSchema($type, $fieldOrDirectiveArgSchemaDefinition[SchemaDefinition::ARGNAME_MANDATORY]);
                    // If it is an input object, it may have its own args to also convert
                    if ($type == SchemaDefinition::TYPE_INPUT_OBJECT) {
                        foreach (($fieldOrDirectiveArgSchemaDefinition[SchemaDefinition::ARGNAME_ARGS] ?? []) as $inputFieldArgName => $inputFieldArgDefinition) {
                            $inputFieldType = $inputFieldArgDefinition[SchemaDefinition::ARGNAME_TYPE];
                            $fieldOrDirectiveSchemaDefinition[SchemaDefinition::ARGNAME_ARGS][$fieldOrDirectiveArgName][SchemaDefinition::ARGNAME_ARGS][$inputFieldArgName][SchemaDefinition::ARGNAME_TYPE] = SchemaHelpers::getFieldOrDirectiveArgTypeToOutputInSchema($inputFieldType, $inputFieldArgDefinition[SchemaDefinition::ARGNAME_MANDATORY]);
                        }
                    }
                }
            }
        }
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

    public function getDynamicTypes(bool $filterRepeated = true): array
    {
        // Watch out! When an ObjectType or InterfaceType implements an interface, and a field of dynamicType (such as "status", which is an ENUM) is covered by the interface,
        // then the field definition will be that one from the interface's perspective
        // Hence, this field may be registered several times, as coming from different ObjectTypes implementing the same interface! (Eg: both Post and Page have field "status" from interface ContentEntry)
        // If $filterRepeated is true, remove instances with a repeated name
        if ($filterRepeated) {
            $dynamicTypes = $typeNames = [];
            foreach ($this->dynamicTypes as $type) {
                $typeName = $type->getName();
                if (!in_array($typeName, $typeNames)) {
                    $dynamicTypes[] = $type;
                    $typeNames[] = $typeName;
                }
            }
            return $dynamicTypes;
        }
        return $this->dynamicTypes;
    }
}
