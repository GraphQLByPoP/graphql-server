<?php
namespace PoP\GraphQL\TypeResolvers;

use PoP\GraphQL\TypeDataLoaders\InputObjectTypeDataLoader;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\TypeResolvers\AbstractTypeResolver;

class InputObjectTypeResolver extends AbstractTypeResolver
{
    public const NAME = '__InputObject';

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
        $inputObject = $resultItem;
        return $inputObject->getID();
    }

    public function getTypeDataLoaderClass(): string
    {
        return InputObjectTypeDataLoader::class;
    }
}

