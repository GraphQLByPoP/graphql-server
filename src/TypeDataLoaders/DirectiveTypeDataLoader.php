<?php
namespace PoP\GraphQL\TypeDataLoaders;

use PoP\GraphQL\ObjectModels\Directive;
use PoP\GraphQL\TypeResolvers\DirectiveTypeResolver;
use PoP\ComponentModel\TypeDataLoaders\AbstractTypeDataLoader;
use PoP\ComponentModel\TypeDataLoaders\UseObjectDictionaryTypeDataLoaderTrait;
use PoP\GraphQL\ObjectModels\TypeUtils;

class DirectiveTypeDataLoader extends AbstractTypeDataLoader
{
    use UseObjectDictionaryTypeDataLoaderTrait;

    protected function getTypeResolverClass(): string
    {
        return DirectiveTypeResolver::class;
    }

    protected function getTypeNewInstance($id): object
    {
        return new Directive(TypeUtils::getSchemaDefinitionPathFromID($id));
    }
}
