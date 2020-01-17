<?php
namespace PoP\GraphQL\TypeDataLoaders;

use PoP\GraphQL\ObjectModels\TypeUtils;
use PoP\GraphQL\ObjectModels\FieldUtils;
use PoP\GraphQL\ObjectModels\InputValue;
use PoP\GraphQL\TypeResolvers\InputValueTypeResolver;
use PoP\ComponentModel\TypeDataLoaders\AbstractTypeDataLoader;
use PoP\ComponentModel\TypeDataLoaders\UseObjectDictionaryTypeDataLoaderTrait;

class InputValueTypeDataLoader extends AbstractTypeDataLoader
{
    use UseObjectDictionaryTypeDataLoaderTrait;

    protected function getTypeResolverClass(): string
    {
        return InputValueTypeResolver::class;
    }

    protected function getTypeNewInstance($id): object
    {
        // list(
        //     $field,
        //     $inputValueName
        // ) = FieldUtils::getFieldAndInputValueNameFromID($id);
        return new InputValue(TypeUtils::getSchemaDefinitionPathFromID($id));
    }
}
