<?php
namespace PoP\GraphQL\TypeResolvers;

use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\GraphQL\TypeResolvers\AbstractIntrospectionTypeResolver;
use PoP\GraphQL\TypeDataLoaders\SchemaDefinitionReferenceTypeDataLoader;

class FieldTypeResolver extends AbstractIntrospectionTypeResolver
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
        return SchemaDefinitionReferenceTypeDataLoader::class;
    }
}
