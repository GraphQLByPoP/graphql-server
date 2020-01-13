<?php
namespace PoP\GraphQL\TypeDataLoaders;

use PoP\GraphQL\ObjectModels\EnumValueType;
use PoP\GraphQL\TypeResolvers\EnumValueTypeResolver;
use PoP\ComponentModel\TypeDataLoaders\AbstractTypeDataLoader;
use PoP\ComponentModel\TypeDataLoaders\UseObjectDictionaryTypeDataLoaderTrait;

class EnumValueTypeDataLoader extends AbstractTypeDataLoader
{
    use UseObjectDictionaryTypeDataLoaderTrait;

    protected function getTypeResolverClass(): string
    {
        return EnumValueTypeResolver::class;
    }

    protected function getTypeNewInstance($id): object
    {
        return new EnumValueType($id);
    }
}
