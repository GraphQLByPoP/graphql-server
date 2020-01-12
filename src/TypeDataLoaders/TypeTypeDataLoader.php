<?php
namespace PoP\GraphQL\TypeDataLoaders;

use PoP\GraphQL\ObjectModels\ObjectType;
use PoP\GraphQL\TypeResolvers\TypeTypeResolver;
use PoP\ComponentModel\TypeDataLoaders\AbstractTypeDataLoader;
use PoP\ComponentModel\TypeDataLoaders\UseObjectDictionaryTypeDataLoaderTrait;

class TypeTypeDataLoader extends AbstractTypeDataLoader
{
    use UseObjectDictionaryTypeDataLoaderTrait;

    protected function getTypeResolverClass(): string
    {
        return TypeTypeResolver::class;
    }

    protected function getTypeNewInstance($id): object
    {
        return new ObjectType($id);
    }
}
