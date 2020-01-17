<?php
namespace PoP\GraphQL\TypeDataLoaders;

use PoP\GraphQL\ObjectModels\Field;
use PoP\GraphQL\ObjectModels\TypeUtils;
use PoP\GraphQL\ObjectModels\FieldUtils;
use PoP\GraphQL\TypeResolvers\FieldTypeResolver;
use PoP\ComponentModel\TypeDataLoaders\AbstractTypeDataLoader;
use PoP\ComponentModel\TypeDataLoaders\UseObjectDictionaryTypeDataLoaderTrait;

class FieldTypeDataLoader extends AbstractTypeDataLoader
{
    use UseObjectDictionaryTypeDataLoaderTrait;

    protected function getTypeResolverClass(): string
    {
        return FieldTypeResolver::class;
    }

    protected function getTypeNewInstance($id): object
    {
        // // From the ID and the typeRegistry we obtain the type
        // list(
        //     $type,
        //     $field
        // ) = FieldUtils::getTypeAndField($id);
        return new Field(TypeUtils::getSchemaDefinitionPathFromID($id));
    }
}
