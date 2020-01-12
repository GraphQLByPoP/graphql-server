<?php
namespace PoP\GraphQL\TypeDataLoaders;

use PoP\GraphQL\ObjectModels\Field;
use PoP\GraphQL\TypeResolvers\FieldTypeResolver;
use PoP\ComponentModel\TypeDataLoaders\AbstractTypeDataLoader;
use PoP\ComponentModel\TypeDataLoaders\UseObjectDictionaryTypeDataLoaderTrait;
use PoP\GraphQL\ObjectModels\FieldUtils;

class FieldTypeDataLoader extends AbstractTypeDataLoader
{
    use UseObjectDictionaryTypeDataLoaderTrait;

    protected function getTypeResolverClass(): string
    {
        return FieldTypeResolver::class;
    }

    protected function getTypeNewInstance($id): object
    {
        // From the ID and the typeRegistry we obtain the type
        // var_dump('id', $id);
        list(
            $type,
            $field
        ) = FieldUtils::getTypeAndField($id);
        return new Field($type, $field);
    }
}
