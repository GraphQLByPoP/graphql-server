<?php
namespace PoP\GraphQL\TypeResolvers;

use PoP\GraphQL\TypeDataLoaders\TypeTypeDataLoader;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\TypeResolvers\AbstractTypeResolver;

class TypeTypeResolver extends AbstractTypeResolver
{
    public const NAME = '__Type';

    public function getTypeName(): string
    {
        return self::NAME;
    }

    public function getSchemaTypeDescription(): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        return $translationAPI->__('Representation of each GraphQL type in the graph', 'graphql');
    }

    public function getID($resultItem)
    {
        $type = $resultItem;
        return $type->getID();
    }

    public function getTypeDataLoaderClass(): string
    {
        return TypeTypeDataLoader::class;
    }
}

