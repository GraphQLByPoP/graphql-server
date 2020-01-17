<?php
namespace PoP\GraphQL\TypeDataLoaders;

use PoP\GraphQL\ObjectModels\Schema;
use PoP\API\TypeResolvers\RootTypeResolver;
use PoP\GraphQL\TypeResolvers\SchemaTypeResolver;
use PoP\ComponentModel\TypeDataLoaders\AbstractTypeDataLoader;
use PoP\ComponentModel\Facades\Instances\InstanceManagerFacade;
use PoP\GraphQL\Facades\Registries\SchemaDefinitionReferenceRegistryFacade;
use PoP\ComponentModel\TypeDataLoaders\UseObjectDictionaryTypeDataLoaderTrait;

class SchemaTypeDataLoader extends AbstractTypeDataLoader
{
    use UseObjectDictionaryTypeDataLoaderTrait;

    protected function getTypeResolverClass(): string
    {
        return SchemaTypeResolver::class;
    }

    protected function getTypeNewInstance($id): object
    {
        return new Schema(
            $this->getSchemaDefinition($id),
            $id,
            $this->getQueryTypeName($id),
            $this->getMutationTypeName($id),
            $this->getSubscriptionTypeName($id)
        );
    }

    protected function &getSchemaDefinition(string $id): array
    {
        $schemaDefinitionReferenceRegistry = SchemaDefinitionReferenceRegistryFacade::getInstance();
        return $schemaDefinitionReferenceRegistry->getFullSchemaDefinition();
    }

    protected function getQueryTypeName(string $id): string
    {
        $instanceManager = InstanceManagerFacade::getInstance();
        $typeResolver = $instanceManager->getInstance(RootTypeResolver::class);
        return $typeResolver->getTypeName();
    }

    protected function getMutationTypeName(string $id): ?string
    {
        return null;
    }

    protected function getSubscriptionTypeName(string $id): ?string
    {
        return null;
    }
}
