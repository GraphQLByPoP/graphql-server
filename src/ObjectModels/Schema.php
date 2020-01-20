<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\API\Schema\SchemaDefinition;
use PoP\GraphQL\Schema\SchemaDefinition as GraphQLSchemaDefinition;
use PoP\GraphQL\ObjectModels\Directive;
use PoP\GraphQL\ObjectModels\ScalarType;
use PoP\GraphQL\ObjectModels\AbstractType;
use PoP\GraphQL\Facades\Registries\SchemaDefinitionReferenceRegistryFacade;
use PoP\GraphQL\Schema\SchemaDefinitionHelpers;

class Schema
{
    protected $id;
    protected $queryTypeResolverInstance;
    protected $mutationTypeResolverInstance;
    protected $subscriptionTypeResolverInstance;
    protected $types;
    protected $directives;
    public function __construct(array $fullSchemaDefinition, string $id, string $queryTypeName, ?string $mutationTypeName = null, ?string $subscriptionTypeName = null)
    {
        $this->id = $id;

        // Remove the introspection fields that must not be added to the schema
        // Field "__typename" from all types (GraphQL spec @ https://graphql.github.io/graphql-spec/draft/#sel-FAJZHABFBKjrL):
        // "This field is implicit and does not appear in the fields list in any defined type."
        unset($fullSchemaDefinition[SchemaDefinition::ARGNAME_GLOBAL_FIELDS]['__typename']);

        // Fields "__schema" and "__type" from the query type (GraphQL spec @ https://graphql.github.io/graphql-spec/draft/#sel-FAJbHABABnD9ub):
        // "These fields are implicit and do not appear in the fields list in the root type of the query operation."
        unset($fullSchemaDefinition[SchemaDefinition::ARGNAME_TYPES][$queryTypeName][SchemaDefinition::ARGNAME_CONNECTIONS]['__type']);
        unset($fullSchemaDefinition[SchemaDefinition::ARGNAME_TYPES][$queryTypeName][SchemaDefinition::ARGNAME_CONNECTIONS]['__schema']);

        // Initialize the global elements before anything, since they will be references from the ObjectType: Fields/Connections/Directives
        // 1. Initialize all the Scalar types
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
        $this->types = [];
        foreach ($scalarTypeNames as $typeName) {
            $typeSchemaDefinitionPath = [
                SchemaDefinition::ARGNAME_TYPES,
                $typeName,
            ];
            $this->types[] = new ScalarType(
                $fullSchemaDefinition,
                $typeSchemaDefinitionPath,
                $typeName
            );
        }

        // 1. Global fields
        SchemaDefinitionHelpers::initFieldsFromPath(
            $fullSchemaDefinition,
            [
                SchemaDefinition::ARGNAME_GLOBAL_FIELDS,
            ]
        );
        // 2. Global connections
        SchemaDefinitionHelpers::initFieldsFromPath(
            $fullSchemaDefinition,
            [
                SchemaDefinition::ARGNAME_GLOBAL_CONNECTIONS,
            ]
        );

        // Initialize the interfaces
        $interfaceSchemaDefinitionPath = [
            SchemaDefinition::ARGNAME_INTERFACES,
        ];
        $interfaceSchemaDefinitionPointer = SchemaDefinitionHelpers::advancePointerToPath(
            $fullSchemaDefinition,
            $interfaceSchemaDefinitionPath
        );
        foreach (array_keys($interfaceSchemaDefinitionPointer) as $interfaceName) {
            new InterfaceType(
                $fullSchemaDefinition,
                array_merge(
                    $interfaceSchemaDefinitionPath,
                    [
                        $interfaceName
                    ]
                )
            );
        }

        // Initialize the directives
        $this->directives = [];
        foreach ($fullSchemaDefinition[SchemaDefinition::ARGNAME_GLOBAL_DIRECTIVES] as $directiveName => $directiveDefinition) {
            $directiveSchemaDefinitionPath = [
                SchemaDefinition::ARGNAME_GLOBAL_DIRECTIVES,
                $directiveName,
            ];
            $this->directives[] = $this->getDirectiveInstance($fullSchemaDefinition, $directiveSchemaDefinitionPath);
        }

        // Initialize the different types
        // 1. queryType
        $queryTypeSchemaDefinitionPath = [
            SchemaDefinition::ARGNAME_TYPES,
            $queryTypeName,
        ];
        $this->queryType = $this->getTypeInstance($fullSchemaDefinition, $queryTypeSchemaDefinitionPath);

        // 2. mutationType
        if ($mutationTypeName) {
            $mutationTypeSchemaDefinitionPath = [
                SchemaDefinition::ARGNAME_TYPES,
                $mutationTypeName,
            ];
            $this->mutationType = $this->getTypeInstance($fullSchemaDefinition, $mutationTypeSchemaDefinitionPath);
        }

        // 3. subscriptionType
        if ($subscriptionTypeName) {
            $subscriptionTypeSchemaDefinitionPath = [
                SchemaDefinition::ARGNAME_TYPES,
                $subscriptionTypeName,
            ];
            $this->subscriptionType = $this->getTypeInstance($fullSchemaDefinition, $subscriptionTypeSchemaDefinitionPath);
        }

        // 2. Initialize the Object and Union types from under "types" and the Interface type from under "interfaces"
        $resolvableTypes = [];
        $resolvableTypeNames = array_diff(
            array_keys($fullSchemaDefinition[SchemaDefinition::ARGNAME_TYPES]),
            $scalarTypeNames
        );
        foreach ($resolvableTypeNames as $typeName) {
            $typeSchemaDefinitionPath = [
                SchemaDefinition::ARGNAME_TYPES,
                $typeName,
            ];
            $resolvableTypes[] = $this->getTypeInstance($fullSchemaDefinition, $typeSchemaDefinitionPath);
        }
        foreach (array_keys($fullSchemaDefinition[SchemaDefinition::ARGNAME_INTERFACES]) as $interfaceName) {
            $interfaceSchemaDefinitionPath = [
                SchemaDefinition::ARGNAME_INTERFACES,
                $interfaceName,
            ];
            $resolvableTypes[] = new InterfaceType(
                $fullSchemaDefinition,
                $interfaceSchemaDefinitionPath
            );
        }

        // 3. Since all types have been initialized by now, we tell them to further initialize their type dependencies, since now they all exist
        // This step will initialize the dynamic Enum and InputObject types and add them to the registry
        foreach ($resolvableTypes as $resolvableType) {
            $resolvableType->initializeTypeDependencies();
        }

        // 4. Add the Object, Union and Interface types under $resolvableTypes, and the dynamic Enum and InputObject types from the registry
        $schemaDefinitionReferenceRegistry = SchemaDefinitionReferenceRegistryFacade::getInstance();
        $this->types = array_merge(
            $this->types,
            $resolvableTypes,
            $schemaDefinitionReferenceRegistry->getDynamicTypes()
        );
    }
    protected function getTypeInstance(array &$fullSchemaDefinition, array $typeSchemaDefinitionPath)
    {
        $typeSchemaDefinitionPointer = &$fullSchemaDefinition;
        foreach ($typeSchemaDefinitionPath as $pathLevel) {
            $typeSchemaDefinitionPointer = &$typeSchemaDefinitionPointer[$pathLevel];
        }
        $typeSchemaDefinition = $typeSchemaDefinitionPointer;
        // The type here can either be an ObjectType or a UnionType
        return $typeSchemaDefinition[SchemaDefinition::ARGNAME_IS_UNION] ?
            new UnionType($fullSchemaDefinition, $typeSchemaDefinitionPath) :
            new ObjectType($fullSchemaDefinition, $typeSchemaDefinitionPath);
    }
    protected function getDirectiveInstance(array &$fullSchemaDefinition, array $directiveSchemaDefinitionPath)
    {
        return new Directive($fullSchemaDefinition, $directiveSchemaDefinitionPath);
    }

    public function getID() {
        return $this->id;
    }
    public function getQueryType(): AbstractType
    {
        return $this->queryType;
    }
    public function getQueryTypeID(): string
    {
        return $this->getQueryType()->getID();
    }
    public function getMutationType(): ?AbstractType
    {
        return $this->mutationType;
    }
    public function getMutationTypeID(): ?string
    {
        if ($mutationType = $this->getMutationType()) {
            return $mutationType->getID();
        }
        return null;
    }
    public function getSubscriptionType(): ?AbstractType
    {
        return $this->subscriptionType;
    }
    public function getSubscriptionTypeID(): ?string
    {
        if ($subscriptionType = $this->getSubscriptionType()) {
            return $subscriptionType->getID();
        }
        return null;
    }

    public function getTypes()
    {
        return $this->types;
    }
    public function getTypeIDs(): array
    {
        return array_map(
            function(AbstractType $type) {
                return $type->getID();
            },
            $this->getTypes()
        );
    }
    public function getDirectives()
    {
        return $this->directives;
    }
    public function getDirectiveIDs(): array
    {
        return array_map(
            function(Directive $directive) {
                return $directive->getID();
            },
            $this->getDirectives()
        );
    }
    public function getType(string $typeName): ?AbstractType
    {
        // From all the types, get the one that has this name
        foreach ($this->types as $type) {
            if ($type->getName() == $typeName) {
                return $type;
            }
        }
        return null;
    }
    public function getTypeID(string $typeName): ?string
    {
        if ($type = $this->getType($typeName)) {
            return $type->getID();
        }
        return null;
    }
}
