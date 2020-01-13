<?php
namespace PoP\GraphQL\FieldResolvers;

use PoP\GraphQL\ObjectModels\TypeKinds;
use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\GraphQL\TypeResolvers\TypeTypeResolver;
use PoP\GraphQL\TypeResolvers\FieldTypeResolver;
use PoP\ComponentModel\Schema\TypeCastingHelpers;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\GraphQL\ObjectModels\HasFieldsTypeInterface;
use PoP\GraphQL\ObjectModels\HasInterfacesTypeInterface;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\ComponentModel\FieldResolvers\AbstractDBDataFieldResolver;
use PoP\ComponentModel\FieldResolvers\EnumTypeSchemaDefinitionResolverTrait;
use PoP\GraphQL\ObjectModels\TypeUtils;

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
            'fields' => $translationAPI->__('Type\'s fields', 'graphql'),
            'interfaces' => $translationAPI->__('Type\'s interfaces', 'graphql'),
        ];
        return $descriptions[$fieldName] ?? parent::getSchemaFieldDescription($typeResolver, $fieldName);
    }

    public function getSchemaFieldArgs(TypeResolverInterface $typeResolver, string $fieldName): array
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        switch ($fieldName) {
            case 'fields':
                return [
                    [
                        SchemaDefinition::ARGNAME_NAME => 'includeDeprecated',
                        SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_BOOL,
                        SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('Include deprecated fields?', 'graphql'),
                        SchemaDefinition::ARGNAME_DEFAULT_VALUE => false,
                    ],
                ];
        }

        return parent::getSchemaFieldArgs($typeResolver, $fieldName);
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
                    return $type->getFields($includeDeprecated);
                }
                return null;
            case 'interfaces':
                // From GraphQL spec (https://graphql.github.io/graphql-spec/draft/#sel-FAJbLACnCCCpCA4yV):
                // "should be non-null for OBJECT only, must be null for the others"
                if ($type instanceof HasInterfacesTypeInterface) {
                    // Return the interfaces through their ID representation: Kind + Name
                    return array_map(
                        function($interfaceName) {
                            return TypeUtils::getID(TypeKinds::INTERFACE, $interfaceName);
                        },
                        $type->getInterfaces()
                    );
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
                return TypeTypeResolver::class;
        }
        return parent::resolveFieldTypeResolverClass($typeResolver, $fieldName, $fieldArgs);
    }
}
