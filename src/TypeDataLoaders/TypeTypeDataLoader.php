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
        $schemaDefinitionPath = TypeUtils::getSchemaDefinitionPathFromID($id);
        $kind = TypeUtils::extractKindFromID($id);
        switch ($kind) {
            case TypeKinds::OBJECT:
                return new ObjectType($schemaDefinitionPath);
            case TypeKinds::INTERFACE:
                return new InterfaceType($schemaDefinitionPath);
            case TypeKinds::UNION:
                return new UnionType($schemaDefinitionPath);
            case TypeKinds::SCALAR:
                return new ScalarType($schemaDefinitionPath);
            case TypeKinds::ENUM:
                return new EnumType($schemaDefinitionPath);
            case TypeKinds::INPUT_OBJECT:
                return new InputObjectType($schemaDefinitionPath);
            case TypeKinds::LIST:
                // The inner elements themselves have no definitionSchemaPath
                $nestedTypeID = TypeUtils::extractNestedTypesFromID($id);
                return new ListType($schemaDefinitionPath, $this->getTypeNewInstance($nestedTypeID));
            case TypeKinds::NON_NULL:
                // The inner elements themselves have no definitionSchemaPath
                $nestedTypeID = TypeUtils::extractNestedTypesFromID($id);
                return new NonNullType($schemaDefinitionPath, $this->getTypeNewInstance($nestedTypeID));
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
