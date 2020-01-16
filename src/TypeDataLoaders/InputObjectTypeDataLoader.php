<?php
namespace PoP\GraphQL\TypeDataLoaders;

use PoP\GraphQL\ObjectModels\TypeUtils;
use PoP\GraphQL\ObjectModels\FieldUtils;
use PoP\GraphQL\ObjectModels\InputObject;
use PoP\GraphQL\ObjectModels\InputObjectType;
use PoP\GraphQL\TypeResolvers\InputObjectTypeResolver;
use PoP\ComponentModel\TypeDataLoaders\AbstractTypeDataLoader;
use PoP\ComponentModel\TypeDataLoaders\UseObjectDictionaryTypeDataLoaderTrait;

class InputObjectTypeDataLoader extends AbstractTypeDataLoader
{
    use UseObjectDictionaryTypeDataLoaderTrait;

    protected function getTypeResolverClass(): string
    {
        return InputObjectTypeResolver::class;
    }

    protected function getTypeNewInstance($id): object
    {
        list(
            $field,
            $inputObjectName
        ) = FieldUtils::getFieldAndInputObjectNameFromID($id);
        return new InputObject($field, $inputObjectName);
    }
}
