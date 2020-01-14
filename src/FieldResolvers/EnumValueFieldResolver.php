<?php
namespace PoP\GraphQL\FieldResolvers;

use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\GraphQL\TypeResolvers\EnumValueTypeResolver;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\ComponentModel\FieldResolvers\AbstractDBDataFieldResolver;

class EnumValueFieldResolver extends AbstractDBDataFieldResolver
{
    public static function getClassesToAttachTo(): array
    {
        return array(EnumValueTypeResolver::class);
    }

    public static function getFieldNamesToResolve(): array
    {
        return [
            'name',
            'description',
            'isDeprecated',
            'deprecatedReason',
        ];
    }

    public function getSchemaFieldType(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $types = [
            'name' => SchemaDefinition::TYPE_STRING,
            'description' => SchemaDefinition::TYPE_STRING,
            'isDeprecated' => SchemaDefinition::TYPE_BOOL,
            'deprecatedReason' => SchemaDefinition::TYPE_STRING,
        ];
        return $types[$fieldName] ?? parent::getSchemaFieldType($typeResolver, $fieldName);
    }

    public function getSchemaFieldDescription(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        $descriptions = [
            'name' => $translationAPI->__('Enum value\'s name as defined by the GraphQL spec (https://graphql.github.io/graphql-spec/draft/#sel-FAJbLACvBBCyBH6rd)', 'graphql'),
            'description' => $translationAPI->__('Enum value\'s description', 'graphqlas defined by the GraphQL spec (https://graphql.github.io/graphql-spec/draft/#sel-FAJbLACyBIC1BHnjL)'),
            'isDeprecated' => $translationAPI->__('Is the enum value deprecated?', 'graphql'),
            'deprecatedReason' => $translationAPI->__('Why was the enum value deprecated?', 'graphql'),
        ];
        return $descriptions[$fieldName] ?? parent::getSchemaFieldDescription($typeResolver, $fieldName);
    }

    public function resolveValue(TypeResolverInterface $typeResolver, $resultItem, string $fieldName, array $fieldArgs = [], ?array $variables = null, ?array $expressions = null, array $options = [])
    {
        $enumValue = $resultItem;
        switch ($fieldName) {
            case 'name':
                return $enumValue->getName();
            case 'description':
                return $enumValue->getDescription();
            case 'isDeprecated':
                return $enumValue->isDeprecated();
            case 'deprecatedReason':
                return $enumValue->getDeprecatedReason();
        }

        return parent::resolveValue($typeResolver, $resultItem, $fieldName, $fieldArgs, $variables, $expressions, $options);
    }
}
