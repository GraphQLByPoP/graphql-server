<?php

declare(strict_types=1);

namespace PoP\GraphQL\TypeResolvers;

use PoP\GraphQL\TypeDataLoaders\SchemaDefinitionReferenceTypeDataLoader;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\GraphQL\TypeResolvers\AbstractIntrospectionTypeResolver;

class InputValueTypeResolver extends AbstractIntrospectionTypeResolver
{
    public const NAME = '__InputValue';

    public function getTypeName(): string
    {
        return self::NAME;
    }

    public function getSchemaTypeDescription(): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        return $translationAPI->__('Representation of an input object in GraphQL', 'graphql');
    }

    public function getID($resultItem)
    {
        $inputValue = $resultItem;
        return $inputValue->getID();
    }

    public function getTypeDataLoaderClass(): string
    {
        return SchemaDefinitionReferenceTypeDataLoader::class;
    }
}
