<?php
namespace PoP\GraphQL\TypeDataLoaders;

use InvalidArgumentException;
use PoP\GraphQL\ObjectModels\TypeKinds;
use PoP\GraphQL\ObjectModels\TypeUtils;
use PoP\GraphQL\ObjectModels\ObjectType;
use PoP\GraphQL\ObjectModels\InterfaceType;
use PoP\GraphQL\TypeResolvers\TypeTypeResolver;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\TypeDataLoaders\AbstractTypeDataLoader;
use PoP\ComponentModel\TypeDataLoaders\UseObjectDictionaryTypeDataLoaderTrait;

class TypeTypeDataLoader extends AbstractTypeDataLoader
{
    use UseObjectDictionaryTypeDataLoaderTrait;

    protected function getTypeResolverClass(): string
    {
        return TypeTypeResolver::class;
    }

    protected function getTypeNewInstance($id): object
    {
        list(
            $kind,
            $name
        ) = TypeUtils::getKindAndName($id);
        switch ($kind) {
            case TypeKinds::OBJECT:
                return new ObjectType($name);
            case TypeKinds::INTERFACE:
                return new InterfaceType($name);
        }

        $translationAPI = TranslationAPIFacade::getInstance();
        throw new InvalidArgumentException(
            sprintf(
            $translationAPI->__('Can\'t create a new \'type\' object, since kind \'%s\' is unsupported (as coming from ID \'%s\'). Only supported kinds are: \'%s\''),
                $kind,
                $id,
                implode($translationAPI->__('\', \''), [
                    TypeKinds::OBJECT,
                    TypeKinds::INTERFACE
                ])
            )
        );
    }
}
