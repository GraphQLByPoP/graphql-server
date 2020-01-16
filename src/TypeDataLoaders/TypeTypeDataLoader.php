<?php
namespace PoP\GraphQL\TypeDataLoaders;

use InvalidArgumentException;
use PoP\GraphQL\ObjectModels\EnumType;
use PoP\GraphQL\ObjectModels\ListType;
use PoP\GraphQL\ObjectModels\TypeKinds;
use PoP\GraphQL\ObjectModels\TypeUtils;
use PoP\GraphQL\ObjectModels\UnionType;
use PoP\GraphQL\ObjectModels\ObjectType;
use PoP\GraphQL\ObjectModels\ScalarType;
use PoP\GraphQL\ObjectModels\NonNullType;
use PoP\GraphQL\ObjectModels\InterfaceType;
use PoP\GraphQL\ObjectModels\InputObjectType;
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
        $kind = TypeUtils::extractKindFromID($id);
        switch ($kind) {
            case TypeKinds::OBJECT:
                $name = TypeUtils::extractNameFromID($id);
                return new ObjectType($name);
            case TypeKinds::INTERFACE:
                $name = TypeUtils::extractNameFromID($id);
                return new InterfaceType($name);
            case TypeKinds::UNION:
                $name = TypeUtils::extractNameFromID($id);
                return new UnionType($name);
            case TypeKinds::SCALAR:
                return new ScalarType();
            case TypeKinds::ENUM:
                // list(
                //     $fieldID,
                //     $enumName
                // ) = TypeUtils::extractFieldIDAndEnumNameFromID($id);
                $fieldID = TypeUtils::extractFieldIDFromID($id);
                return new EnumType($fieldID/*, $enumName*/);
            case TypeKinds::INPUT_OBJECT:
                return new InputObjectType();
            case TypeKinds::LIST:
                $nestedTypes = TypeUtils::extractNestedTypesFromID($id);
                return new ListType($nestedTypes);
            case TypeKinds::NON_NULL:
                $nestedTypes = TypeUtils::extractNestedTypesFromID($id);
                return new NonNullType($nestedTypes);
        }

        $translationAPI = TranslationAPIFacade::getInstance();
        throw new InvalidArgumentException(
            sprintf(
                $translationAPI->__('Can\'t create a new \'type\' object, since kind \'%s\' is unsupported (as coming from ID \'%s\'). Only supported kinds are: \'%s\''),
                $kind,
                $id,
                implode($translationAPI->__('\', \''), [
                    TypeKinds::OBJECT,
                    TypeKinds::INTERFACE,
                    TypeKinds::UNION,
                    TypeKinds::SCALAR,
                    TypeKinds::ENUM,
                    TypeKinds::INPUT_OBJECT,
                    TypeKinds::LIST,
                    TypeKinds::NON_NULL,
                ])
            )
        );
    }
}
