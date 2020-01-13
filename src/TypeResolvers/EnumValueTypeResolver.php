<?php
namespace PoP\GraphQL\TypeResolvers;

use PoP\GraphQL\TypeDataLoaders\EnumValueTypeDataLoader;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\TypeResolvers\AbstractTypeResolver;

class EnumValueTypeResolver extends AbstractTypeResolver
{
    public const NAME = '__EnumValue';

    public function getTypeName(): string
    {
        return self::NAME;
    }

    public function getSchemaTypeDescription(): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        return $translationAPI->__('Representation of an Enum value in GraphQL', 'graphql');
    }

    public function getID($resultItem)
    {
        $enumValue = $resultItem;
        return $enumValue->getID();
    }

    public function getTypeDataLoaderClass(): string
    {
        return EnumValueTypeDataLoader::class;
    }
}

