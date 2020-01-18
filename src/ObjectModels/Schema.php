<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\API\Schema\SchemaDefinition;
use PoP\GraphQL\Schema\SchemaDefinition as GraphQLSchemaDefinition;
use PoP\GraphQL\ObjectModels\Directive;
use PoP\GraphQL\ObjectModels\ScalarType;
use PoP\GraphQL\ObjectModels\AbstractType;
use PoP\GraphQL\Schema\SchemaHelpers;
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
    public function __construct(array &$fullSchemaDefinition, string $id, string $queryTypeName, ?string $mutationTypeName = null, ?string $subscriptionTypeName = null)
    {
        $this->id = $id;

        // Initialize the global elements before anything, since they will be references from the ObjectType: Fields/Connections/Directives
        // 1. Initialize all the Scalar types
        $scalarTypeNames = [
            // GraphQLSchemaDefinition::TYPE_UNRESOLVED_ID,
            GraphQLSchemaDefinition::TYPE_ID,
            GraphQLSchemaDefinition::TYPE_STRING,
            GraphQLSchemaDefinition::TYPE_INT,
            GraphQLSchemaDefinition::TYPE_FLOAT,
            GraphQLSchemaDefinition::TYPE_BOOL,
            // GraphQLSchemaDefinition::TYPE_ENUM,
            GraphQLSchemaDefinition::TYPE_OBJECT,
            GraphQLSchemaDefinition::TYPE_MIXED,
            GraphQLSchemaDefinition::TYPE_DATE,
            GraphQLSchemaDefinition::TYPE_TIME,
            GraphQLSchemaDefinition::TYPE_URL,
            GraphQLSchemaDefinition::TYPE_EMAIL,
            GraphQLSchemaDefinition::TYPE_IP,
        ];
        // // Convert them to the GraphQL standard: Title case for the Types
        // $scalarTypeNames = array_map(
        //     function($scalarTypeName) {
        //         return SchemaHelpers::convertTypeNameToGraphQLStandard($scalarTypeName);
        //     },
        //     $scalarTypeNames
        // );
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
            $this->directives[] = $this->getDirective($fullSchemaDefinition, $directiveSchemaDefinitionPath);
        }

        // Initialize the different types
        // 1. queryType
        $queryTypeSchemaDefinitionPath = [
            SchemaDefinition::ARGNAME_TYPES,
            $queryTypeName,
        ];
        $this->queryType = $this->getType($fullSchemaDefinition, $queryTypeSchemaDefinitionPath);

        // 2. mutationType
        if ($mutationTypeName) {
            $mutationTypeSchemaDefinitionPath = [
                SchemaDefinition::ARGNAME_TYPES,
                $mutationTypeName,
            ];
            $this->mutationType = $this->getType($fullSchemaDefinition, $mutationTypeSchemaDefinitionPath);
        }

        // 3. subscriptionType
        if ($subscriptionTypeName) {
            $subscriptionTypeSchemaDefinitionPath = [
                SchemaDefinition::ARGNAME_TYPES,
                $subscriptionTypeName,
            ];
            $this->subscriptionType = $this->getType($fullSchemaDefinition, $subscriptionTypeSchemaDefinitionPath);
        }

        // 2. Initialize all the TypeResolver types
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
            $resolvableTypes[] = $this->getType($fullSchemaDefinition, $typeSchemaDefinitionPath);
        }
        $this->types = array_merge(
            $this->types,
            $resolvableTypes
        );

        // 3. Since all types have been initialized by now, we tell them to further initialize their type dependencies, since now they all exist
        foreach ($resolvableTypes as $resolvableType) {
            $resolvableType->initializeTypeDependencies();
        }

        // 4. Initialize all the Interface types
        foreach (array_keys($fullSchemaDefinition[SchemaDefinition::ARGNAME_INTERFACES]) as $interfaceName) {
            $interfaceSchemaDefinitionPath = [
                SchemaDefinition::ARGNAME_INTERFACES,
                $interfaceName,
            ];
            $this->types[] = new InterfaceType(
                $fullSchemaDefinition,
                $interfaceSchemaDefinitionPath
            );
        }

        // 4. After initializing everything, we can include the dynamic types
        $schemaDefinitionReferenceRegistry = SchemaDefinitionReferenceRegistryFacade::getInstance();
        $this->types = array_merge(
            $this->types,
            $schemaDefinitionReferenceRegistry->getDynamicTypes()
        );
    }
    protected function getType(array &$fullSchemaDefinition, array $typeSchemaDefinitionPath)
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
    protected function getDirective(array &$fullSchemaDefinition, array $directiveSchemaDefinitionPath)
    {
        return new Directive($fullSchemaDefinition, $directiveSchemaDefinitionPath);
    }

    public function getID() {
        return $this->id;
    }
    // public function getQueryTypeResolverInstance(): TypeResolverInterface
    // {
    //     $instanceManager = InstanceManagerFacade::getInstance();
    //     return $instanceManager->getInstance(RootTypeResolver::class);
    // }
    // public function getMutationTypeResolverInstance(): ?TypeResolverInterface
    // {
    //     return null;
    // }
    // public function getSubscriptionTypeResolverInstance(): ?TypeResolverInterface
    // {
    //     return null;
    // }
    public function getQueryTypeID(): string
    {
        return $this->queryType->getID();
    }
    public function getMutationTypeID(): ?string
    {
        if ($this->mutationType) {
            return $this->mutationType->getID();
        }
        return null;
    }
    public function getSubscriptionTypeID(): ?string
    {
        if ($this->subscriptionType) {
            return $this->subscriptionType->getID();
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
}
