<?php
namespace PoP\GraphQL\TypeResolvers;

use PoP\GraphQL\TypeDataLoaders\FieldTypeDataLoader;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\TypeResolvers\AbstractTypeResolver;

class FieldTypeResolver extends AbstractTypeResolver
{
    public const NAME = '__Field';

    public function getTypeName(): string
    {
        return self::NAME;
    }

    public function getSchemaTypeDescription(): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        return $translationAPI->__('Representation of a GraphQL type\'s field', 'graphql');
    }

    public function getID($resultItem)
    {
        $field = $resultItem;
        return $field->getID();
    }

    public function getTypeDataLoaderClass(): string
    {
        return FieldTypeDataLoader::class;
    }
}

