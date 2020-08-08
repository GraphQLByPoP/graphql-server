<?php

declare(strict_types=1);

namespace PoP\GraphQLServer\TypeResolvers;

use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\GraphQLServer\TypeResolvers\AbstractIntrospectionTypeResolver;
use PoP\GraphQLServer\TypeDataLoaders\SchemaDefinitionReferenceTypeDataLoader;

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
        return $translationAPI->__('Representation of a GraphQL type\'s field', 'graphql-server');
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
