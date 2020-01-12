<?php
namespace PoP\GraphQL\FieldResolvers;

use PoP\GraphQL\ObjectModels\TypeKinds;
use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\GraphQL\TypeResolvers\TypeTypeResolver;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\ComponentModel\FieldResolvers\AbstractDBDataFieldResolver;
use PoP\ComponentModel\FieldResolvers\EnumTypeSchemaDefinitionResolverTrait;

class TypeFieldResolver extends AbstractDBDataFieldResolver
{
    use EnumTypeSchemaDefinitionResolverTrait;

    public static function getClassesToAttachTo(): array
    {
        return array(TypeTypeResolver::class);
    }

    public static function getFieldNamesToResolve(): array
    {
        return [
            'kind',
            'name',
            'description',
        ];
    }

    public function getSchemaFieldType(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $types = [
            'kind' => SchemaDefinition::TYPE_ENUM,
            'name' => SchemaDefinition::TYPE_STRING,
            'description' => SchemaDefinition::TYPE_STRING,
        ];
        return $types[$fieldName] ?? parent::getSchemaFieldType($typeResolver, $fieldName);
    }

    protected function getSchemaDefinitionEnumValues(TypeResolverInterface $typeResolver, string $fieldName): ?array
    {
        switch ($fieldName) {
            case 'kind':
                return [
                    TypeKinds::SCALAR,
                    TypeKinds::OBJECT,
                    TypeKinds::INTERFACE,
                    TypeKinds::UNION,
                    TypeKinds::ENUM,
                    TypeKinds::INPUT_OBJECT,
                    TypeKinds::LIST,
                    TypeKinds::NON_NULL,
                ];
        }
        return null;
    }

    public function getSchemaFieldDescription(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        $descriptions = [
            'kind' => $translationAPI->__('Type\'s kind', 'graphql'),
            'name' => $translationAPI->__('Type\'s name', 'graphql'),
            'description' => $translationAPI->__('Type\'s description', 'graphql'),
        ];
        return $descriptions[$fieldName] ?? parent::getSchemaFieldDescription($typeResolver, $fieldName);
    }

    public function resolveValue(TypeResolverInterface $typeResolver, $resultItem, string $fieldName, array $fieldArgs = [], ?array $variables = null, ?array $expressions = null, array $options = [])
    {
        $type = $resultItem;
        switch ($fieldName) {
            case 'kind':
                return $type->getKind();
            case 'name':
                return $type->getName();
            case 'description':
                return $type->getDescription();
        }

        return parent::resolveValue($typeResolver, $resultItem, $fieldName, $fieldArgs, $variables, $expressions, $options);
    }
}
