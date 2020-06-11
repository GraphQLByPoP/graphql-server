<?php

declare(strict_types=1);

namespace PoP\GraphQL\FieldResolvers;

use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\GraphQL\ObjectModels\DirectiveLocations;
use PoP\ComponentModel\Schema\TypeCastingHelpers;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\GraphQL\TypeResolvers\DirectiveTypeResolver;
use PoP\GraphQL\TypeResolvers\InputValueTypeResolver;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\ComponentModel\FieldResolvers\AbstractDBDataFieldResolver;
use PoP\ComponentModel\FieldResolvers\EnumTypeSchemaDefinitionResolverTrait;

class DirectiveFieldResolver extends AbstractDBDataFieldResolver
{
    use EnumTypeSchemaDefinitionResolverTrait;

    public static function getClassesToAttachTo(): array
    {
        return array(DirectiveTypeResolver::class);
    }

    public static function getFieldNamesToResolve(): array
    {
        return [
            'name',
            'description',
            'args',
            'locations',
        ];
    }

    public function getSchemaFieldType(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $types = [
            'name' => SchemaDefinition::TYPE_STRING,
            'description' => SchemaDefinition::TYPE_STRING,
            'args' => TypeCastingHelpers::makeArray(SchemaDefinition::TYPE_ID),
            'locations' => SchemaDefinition::TYPE_ENUM,
        ];
        return $types[$fieldName] ?? parent::getSchemaFieldType($typeResolver, $fieldName);
    }

    public function isSchemaFieldResponseNonNullable(TypeResolverInterface $typeResolver, string $fieldName): bool
    {
        $nonNullableFieldNames = [
            'name',
            'args',
            'locations',
        ];
        if (in_array($fieldName, $nonNullableFieldNames)) {
            return true;
        }
        return parent::isSchemaFieldResponseNonNullable($typeResolver, $fieldName);
    }

    protected function getSchemaDefinitionEnumValues(TypeResolverInterface $typeResolver, string $fieldName): ?array
    {
        switch ($fieldName) {
            case 'locations':
                return [
                    DirectiveLocations::QUERY,
                    DirectiveLocations::MUTATION,
                    DirectiveLocations::SUBSCRIPTION,
                    DirectiveLocations::FIELD,
                    DirectiveLocations::FRAGMENT_DEFINITION,
                    DirectiveLocations::FRAGMENT_SPREAD,
                    DirectiveLocations::INLINE_FRAGMENT,
                    DirectiveLocations::SCHEMA,
                    DirectiveLocations::SCALAR,
                    DirectiveLocations::OBJECT,
                    DirectiveLocations::FIELD_DEFINITION,
                    DirectiveLocations::ARGUMENT_DEFINITION,
                    DirectiveLocations::INTERFACE,
                    DirectiveLocations::UNION,
                    DirectiveLocations::ENUM,
                    DirectiveLocations::ENUM_VALUE,
                    DirectiveLocations::INPUT_OBJECT,
                    DirectiveLocations::INPUT_FIELD_DEFINITION,
                ];
        }
        return null;
    }

    public function getSchemaFieldDescription(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        $descriptions = [
            'name' => $translationAPI->__('Directive\'s name', 'graphql'),
            'description' => $translationAPI->__('Directive\'s description', 'graphql'),
            'args' => $translationAPI->__('Directive\'s arguments', 'graphql'),
            'locations' => $translationAPI->__('The locations where the directive may be placed', 'graphql'),
        ];
        return $descriptions[$fieldName] ?? parent::getSchemaFieldDescription($typeResolver, $fieldName);
    }

    public function resolveValue(TypeResolverInterface $typeResolver, $resultItem, string $fieldName, array $fieldArgs = [], ?array $variables = null, ?array $expressions = null, array $options = [])
    {
        $directive = $resultItem;
        switch ($fieldName) {
            case 'name':
                return $directive->getName();
            case 'description':
                return $directive->getDescription();
            case 'args':
                return $directive->getArgIDs();
            case 'locations':
                return $directive->getLocations();
        }

        return parent::resolveValue($typeResolver, $resultItem, $fieldName, $fieldArgs, $variables, $expressions, $options);
    }

    public function resolveFieldTypeResolverClass(TypeResolverInterface $typeResolver, string $fieldName, array $fieldArgs = []): ?string
    {
        switch ($fieldName) {
            case 'args':
                return InputValueTypeResolver::class;
        }
        return parent::resolveFieldTypeResolverClass($typeResolver, $fieldName, $fieldArgs);
    }
}
