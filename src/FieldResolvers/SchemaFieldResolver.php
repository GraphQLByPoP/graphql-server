<?php
namespace PoP\GraphQL\FieldResolvers;

use PoP\API\Schema\SchemaDefinition;
use PoP\GraphQL\ObjectModels\TypeKinds;
use PoP\GraphQL\ObjectModels\TypeUtils;
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
        ];
        return $descriptions[$fieldName] ?? parent::getSchemaFieldDescription($typeResolver, $fieldName);
    }

    public function resolveValue(TypeResolverInterface $typeResolver, $resultItem, string $fieldName, array $fieldArgs = [], ?array $variables = null, ?array $expressions = null, array $options = [])
    {
        $schema = $resultItem;
        switch ($fieldName) {
            case 'queryType':
                return TypeUtils::getID(TypeKinds::OBJECT, $schema->getQueryTypeResolverInstance()->getTypeName());
            case 'mutationType':
                if ($typeResolverInstance = $schema->getMutationTypeResolverInstance()) {
                    return TypeUtils::getID(TypeKinds::OBJECT, $typeResolverInstance->getTypeName());
                }
                return null;
            case 'subscriptionType':
                if ($typeResolverInstance = $schema->getSubscriptionTypeResolverInstance()) {
                    return TypeUtils::getID(TypeKinds::OBJECT, $typeResolverInstance->getTypeName());
                }
                return null;
            case 'types':
                // Return the interfaces through their ID representation: Kind + Name
                return array_map(
                    function($typeName) {
                        return TypeUtils::getID(TypeKinds::OBJECT, $typeName);
                    },
                    $schema->getTypes()
                );
            case 'directives':
                return $schema->getDirectives();
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
                return TypeTypeResolver::class;
            case 'directives':
                return DirectiveTypeResolver::class;
        }

        return parent::resolveFieldTypeResolverClass($typeResolver, $fieldName, $fieldArgs);
    }
}
