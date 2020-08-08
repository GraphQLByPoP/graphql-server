<?php

declare(strict_types=1);

namespace PoP\GraphQLServer\TypeDataLoaders;

use PoP\GraphQLServer\ObjectModels\Schema;
use PoP\GraphQLServer\TypeResolvers\SchemaTypeResolver;
use PoP\ComponentModel\TypeDataLoaders\AbstractTypeDataLoader;
use PoP\GraphQLServer\Facades\Registries\SchemaDefinitionReferenceRegistryFacade;
use PoP\ComponentModel\TypeDataLoaders\UseObjectDictionaryTypeDataLoaderTrait;

class SchemaTypeDataLoader extends AbstractTypeDataLoader
{
    use UseObjectDictionaryTypeDataLoaderTrait;

    protected function getTypeResolverClass(): string
    {
        return SchemaTypeResolver::class;
    }

    protected function getTypeNewInstance($id)
    {
        return new Schema(
            $this->getSchemaDefinition($id),
            $id
        );
    }

    protected function &getSchemaDefinition(string $id): array
    {
        $schemaDefinitionReferenceRegistry = SchemaDefinitionReferenceRegistryFacade::getInstance();
        return $schemaDefinitionReferenceRegistry->getFullSchemaDefinition();
    }
}
