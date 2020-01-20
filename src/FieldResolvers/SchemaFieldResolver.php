<?php
namespace PoP\GraphQL\FieldResolvers;

use PoP\API\Schema\SchemaDefinition;
use PoP\GraphQL\TypeResolvers\TypeTypeResolver;
use PoP\ComponentModel\Schema\TypeCastingHelpers;
use PoP\GraphQL\TypeResolvers\SchemaTypeResolver;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\GraphQL\TypeResolvers\DirectiveTypeResolver;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\ComponentModel\FieldResolvers\AbstractDBDataFieldResolver;

class SchemaFieldResolver extends AbstractDBDataFieldResolver
{
    public static function getClassesToAttachTo(): array
    {
        return array(SchemaTypeResolver::class);
    }

    public static function getFieldNamesToResolve(): array
    {
        return [
            'queryType',
            'mutationType',
            'subscriptionType',
            'types',
            'directives',
            'type',
        ];
    }

    public function getSchemaFieldType(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $types = [
            'queryType' => SchemaDefinition::TYPE_ID,
            'mutationType' => SchemaDefinition::TYPE_ID,
            'subscriptionType' => SchemaDefinition::TYPE_ID,
            'types' => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_ID),
            'directives' => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_ID),
            'type' => SchemaDefinition::TYPE_ID,
        ];
        return $types[$fieldName] ?? parent::getSchemaFieldType($typeResolver, $fieldName);
    }

    public function getSchemaFieldDescription(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        $descriptions = [
            'queryType' => $translationAPI->__('The type, accessible from the root, that resolves queries', 'graphql'),
            'mutationType' => $translationAPI->__('The type, accessible from the root, that resolves mutations', 'graphql'),
            'subscriptionType' => $translationAPI->__('The type, accessible from the root, that resolves subscriptions', 'graphql'),
            'types' => $translationAPI->__('All types registered in the data graph', 'graphql'),
            'directives' => $translationAPI->__('All directives registered in the data graph', 'graphql'),
            'type' => $translationAPI->__('Obtain a specific type from the schema', 'graphql'),
        ];
        return $descriptions[$fieldName] ?? parent::getSchemaFieldDescription($typeResolver, $fieldName);
    }

    public function getSchemaFieldArgs(TypeResolverInterface $typeResolver, string $fieldName): array
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        switch ($fieldName) {
            case 'type':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'name',
                        SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_STRING,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The name of the type', 'graphql'),
                        SchemaDefinition::ARGNAME_MANDATORY => true,
                    ],
                ];
        }

        return parent::getSchemaFieldArgs($typeResolver, $fieldName);
    }

    public function resolveValue(TypeResolverInterface $typeResolver, $resultItem, string $fieldName, array $fieldArgs = [], ?array $variables = null, ?array $expressions = null, array $options = [])
    {
        $schema = $resultItem;
        switch ($fieldName) {
            case 'queryType':
                return $schema->getQueryTypeID();
            case 'mutationType':
                return $schema->getMutationTypeID();
            case 'subscriptionType':
                return $schema->getSubscriptionTypeID();
            case 'types':
                return $schema->getTypeIDs();
            case 'directives':
                return $schema->getDirectiveIDs();
            case 'type':
                return $schema->getTypeID($fieldArgs['name']);
        }

        return parent::resolveValue($typeResolver, $resultItem, $fieldName, $fieldArgs, $variables, $expressions, $options);
    }

    public function resolveFieldTypeResolverClass(TypeResolverInterface $typeResolver, string $fieldName, array $fieldArgs = []): ?string
    {
        switch ($fieldName) {
            case 'queryType':
            case 'mutationType':
            case 'subscriptionType':
            case 'types':
            case 'type':
                return TypeTypeResolver::class;
            case 'directives':
                return DirectiveTypeResolver::class;
        }

        return parent::resolveFieldTypeResolverClass($typeResolver, $fieldName, $fieldArgs);
    }
}
