<?php

declare(strict_types=1);

namespace PoP\GraphQLServer\TypeResolvers;

use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\GraphQLServer\TypeResolvers\AbstractIntrospectionTypeResolver;
use PoP\GraphQLServer\TypeDataLoaders\SchemaDefinitionReferenceTypeDataLoader;

class EnumValueTypeResolver extends AbstractIntrospectionTypeResolver
{
    public const NAME = '__EnumValue';

    public function getTypeName(): string
    {
        return self::NAME;
    }

    public function getSchemaTypeDescription(): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        return $translationAPI->__('Representation of an Enum value in GraphQL', 'graphql-server');
    }

    public function getID($resultItem)
    {
        $enumValue = $resultItem;
        return $enumValue->getID();
    }

    public function getTypeDataLoaderClass(): string
    {
        return SchemaDefinitionReferenceTypeDataLoader::class;
    }
}
