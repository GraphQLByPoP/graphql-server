<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\API\Schema\SchemaDefinition;
use PoP\API\ObjectFacades\RootObjectFacade;
use PoP\API\TypeResolvers\RootTypeResolver;
use PoP\GraphQL\Facades\Registries\TypeRegistryFacade;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\ComponentModel\Facades\Instances\InstanceManagerFacade;
use PoP\ComponentModel\Facades\Schema\FieldQueryInterpreterFacade;

class Schema
{
    protected $id, $fullSchema;
    public function __construct(string $id)
    {
        $this->id = $id;
    }
    public function getID() {
        return $this->id;
    }
    public function getQueryTypeResolverInstance(): TypeResolverInterface
    {
        $instanceManager = InstanceManagerFacade::getInstance();
        return $instanceManager->getInstance(RootTypeResolver::class);
    }
    public function getMutationTypeResolverInstance(): ?TypeResolverInterface
    {
        return null;
    }
    public function getSubscriptionTypeResolverInstance(): ?TypeResolverInterface
    {
        return null;
    }
    public function maybeInitializeFullSchema()
    {
        // Lazy init the fullSchema
        if (is_null($this->fullSchema)) {
            $rootTypeResolver = $this->getQueryTypeResolverInstance();
            $fieldQueryInterpreter = FieldQueryInterpreterFacade::getInstance();
            $root = RootObjectFacade::getInstance();
            $fieldArgs = [
                'deep' => true,
                'shape' => SchemaDefinition::ARGVALUE_SCHEMA_SHAPE_FLAT,
                'compressed' => true,
                'typeAsSDL' => false,
                'readable' => true,
            ];
            $options = [
                'use-type-resolver-class-as-schema-key' => true,
            ];
            $this->fullSchema = $rootTypeResolver->resolveValue(
                $root,
                $fieldQueryInterpreter->getField('__fullSchema', $fieldArgs),
                null,
                null,
                $options
            );

            // Register all the types in the TypeRegistry
            $typeRegistry = TypeRegistryFacade::getInstance();
            foreach ($this->fullSchema[SchemaDefinition::ARGNAME_TYPES] as $typeResolverClass => $typeDefinition) {
                $typeName = $typeDefinition[SchemaDefinition::ARGNAME_NAME];
                $typeRegistry->registerType($typeName, $typeResolverClass);
            }
        }
    }
    public function getTypes()
    {
        // Lazy init the fullSchema
        $this->maybeInitializeFullSchema();

        $typeRegistry = TypeRegistryFacade::getInstance();
        return $typeRegistry->getTypeNames();
    }
    public function getDirectives()
    {
        // Lazy init the fullSchema
        $this->maybeInitializeFullSchema();

        // Extract the types from the fullSchema
        return array_keys($this->fullSchema[SchemaDefinition::ARGNAME_GLOBAL_DIRECTIVES]);
    }
}
