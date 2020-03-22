<?php
namespace PoP\GraphQL\FieldResolvers;

use PoP\GraphQL\ObjectModels\EnumType;
use PoP\GraphQL\ObjectModels\TypeKinds;
use PoP\GraphQL\ObjectModels\InputObjectType;
use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\GraphQL\TypeResolvers\TypeTypeResolver;
use PoP\GraphQL\TypeResolvers\FieldTypeResolver;
use PoP\ComponentModel\Schema\TypeCastingHelpers;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\GraphQL\ObjectModels\HasFieldsTypeInterface;
use PoP\GraphQL\TypeResolvers\EnumValueTypeResolver;
use PoP\GraphQL\TypeResolvers\InputValueTypeResolver;
use PoP\GraphQL\ObjectModels\AbstractNestableType;
use PoP\GraphQL\ObjectModels\HasInterfacesTypeInterface;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\GraphQL\ObjectModels\HasPossibleTypesTypeInterface;
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
            'fields',
            'interfaces',
            'possibleTypes',
            'enumValues',
            'inputFields',
            'ofType',
        ];
    }

    public function getSchemaFieldType(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $types = [
            'kind' => SchemaDefinition::TYPE_ENUM,
            'name' => SchemaDefinition::TYPE_STRING,
            'description' => SchemaDefinition::TYPE_STRING,
            'fields' => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_ID),
            'interfaces' => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_ID),
            'possibleTypes' => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_ID),
            'enumValues' => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_ID),
            'inputFields' => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_ID),
            'ofType' => SchemaDefinition::TYPE_ID,
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
            'kind' => $translationAPI->__('Type\'s kind as defined by the GraphQL spec (https://graphql.github.io/graphql-spec/draft/#sel-FAJbLACqBBCvBAtrC)', 'graphql'),
            'name' => $translationAPI->__('Type\'s name as defined by the GraphQL spec (https://graphql.github.io/graphql-spec/draft/#sel-FAJbLACvBBCyBH6rd)', 'graphql'),
            'description' => $translationAPI->__('Type\'s description as defined by the GraphQL spec (https://graphql.github.io/graphql-spec/draft/#sel-FAJbLACyBIC1BHnjL)', 'graphql'),
            'fields' => $translationAPI->__('Type\'s fields (available for Object and Interface types only) as defined by the GraphQL spec (https://graphql.github.io/graphql-spec/draft/#sel-FAJbLAC3BBCnCA8pY)', 'graphql'),
            'interfaces' => $translationAPI->__('Type\'s interfaces (available for Object type only) as defined by the GraphQL spec (https://graphql.github.io/graphql-spec/draft/#sel-FAJbLACpCBCxCA7tB)', 'graphql'),
            'possibleTypes' => $translationAPI->__('Type\'s possible types (available for Interface and Union types only) as defined by the GraphQL spec (https://graphql.github.io/graphql-spec/draft/#sel-FAJbLACzCBC7CA0vN)', 'graphql'),
            'enumValues' => $translationAPI->__('Type\'s enum values (available for Enum type only) as defined by the GraphQL spec (https://graphql.github.io/graphql-spec/draft/#sel-FAJbLAC9CDD_CAA2lB)', 'graphql'),
            'inputFields' => $translationAPI->__('Type\'s input Fields (available for InputObject type only) as defined by the GraphQL spec (https://graphql.github.io/graphql-spec/draft/#sel-HAJbLAuDABCBIu9N)', 'graphql'),
            'ofType' => $translationAPI->__('The type of the nested type (available for NonNull and List types only) as defined by the GraphQL spec (https://graphql.github.io/graphql-spec/draft/#sel-HAJbLA4DABCBIu9N)', 'graphql'),
        ];
        return $descriptions[$fieldName] ?? parent::getSchemaFieldDescription($typeResolver, $fieldName);
    }

    public function getSchemaFieldArgs(TypeResolverInterface $typeResolver, string $fieldName): array
    {
        $schemaFieldArgs = parent::getSchemaFieldArgs($typeResolver, $fieldName);
        $translationAPI = TranslationAPIFacade::getInstance();
        switch ($fieldName) {
            case 'fields':
            case 'enumValues':
                return array_merge(
                    $schemaFieldArgs,
                    [
                        [
                            SchemaDefinition::ARGNAME_NAME => 'includeDeprecated',
                            SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_BOOL,
                            SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('Include deprecated fields?', 'graphql'),
                            SchemaDefinition::ARGNAME_DEFAULT_VALUE => false,
                        ],
                    ]
                );
        }

        return $schemaFieldArgs;
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
            case 'fields':
                // From GraphQL spec (https://graphql.github.io/graphql-spec/draft/#sel-FAJbLAC1BJC3BAn6e):
                // "should be non-null for OBJECT and INTERFACE only, must be null for the others"
                if ($type instanceof HasFieldsTypeInterface) {
                    $includeDeprecated = $fieldArgs['includeDeprecated'] ?? false;
                    return $type->getFieldIDs($includeDeprecated);
                }
                return null;
            case 'interfaces':
                // From GraphQL spec (https://graphql.github.io/graphql-spec/draft/#sel-FAJbLACnCCCpCA4yV):
                // "should be non-null for OBJECT only, must be null for the others"
                if ($type instanceof HasInterfacesTypeInterface) {
                    return $type->getInterfaceIDs();
                }
                return null;
            case 'possibleTypes':
                // From GraphQL spec (https://graphql.github.io/graphql-spec/draft/#sel-FAJbLACxCCCzCA_9R):
                // "should be non-null for INTERFACE and UNION only, always null for the others"
                if ($type instanceof HasPossibleTypesTypeInterface) {
                    return $type->getPossibleTypeIDs();
                }
                return null;
            case 'enumValues':
                // From GraphQL spec (https://graphql.github.io/graphql-spec/draft/#sel-FAJbLAC7CCC9CA2nT):
                // "should be non-null for ENUM only, must be null for the others"
                if ($type instanceof EnumType) {
                    $includeDeprecated = $fieldArgs['includeDeprecated'] ?? false;
                    return $type->getEnumValueIDs($includeDeprecated);
                }
                return null;
            case 'inputFields':
                // From GraphQL spec (https://graphql.github.io/graphql-spec/draft/#sel-HAJbLAuDABCBIu9N):
                // "should be non-null for INPUT_OBJECT only, must be null for the others"
                if ($type instanceof InputObjectType) {
                    return $type->getInputFieldIDs();
                }
                return null;
            case 'ofType':
                // From GraphQL spec (https://graphql.github.io/graphql-spec/draft/#sel-HAJbLA4DABCBIu9N):
                // "should be non-null for NON_NULL and LIST only, must be null for the others"
                if ($type instanceof AbstractNestableType) {
                    return $type->getNestedTypeID();
                }
                return null;
        }

        return parent::resolveValue($typeResolver, $resultItem, $fieldName, $fieldArgs, $variables, $expressions, $options);
    }

    public function resolveFieldTypeResolverClass(TypeResolverInterface $typeResolver, string $fieldName, array $fieldArgs = []): ?string
    {
        switch ($fieldName) {
            case 'fields':
                return FieldTypeResolver::class;
            case 'interfaces':
            case 'possibleTypes':
            case 'ofType':
                return TypeTypeResolver::class;
            case 'enumValues':
                return EnumValueTypeResolver::class;
            case 'inputFields':
                return InputValueTypeResolver::class;
        }
        return parent::resolveFieldTypeResolverClass($typeResolver, $fieldName, $fieldArgs);
    }
}
