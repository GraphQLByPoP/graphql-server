<?php
namespace PoP\GraphQL\Schema;

use InvalidArgumentException;
use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\GraphQL\Schema\SchemaDefinition as GraphQLSchemaDefinition;
use PoP\ComponentModel\Schema\TypeCastingHelpers;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\ComponentModel\Facades\Instances\InstanceManagerFacade;

class SchemaHelpers
{
    /**
     * Convert the field type from its internal representation (eg: "array:id") to the GraphQL standard representation (eg: "[Post]")
     *
     * @param TypeResolverInterface $typeResolver
     * @param string $fieldName
     * @param string $type
     * @return void
     */
    public static function getFieldTypeToOutputInSchema(string $type, TypeResolverInterface $typeResolver, string $fieldName, ?bool $isMandatory = false): string
    {
        list (
            $arrayInstances,
            $convertedType
        ) = self::getTypeComponents($type);

        // If the type is an ID, replace it with the actual type the ID references
        if ($convertedType == SchemaDefinition::TYPE_ID) {
            $instanceManager = InstanceManagerFacade::getInstance();
            // The convertedType may not be implemented yet (eg: Category), then skip
            if ($fieldTypeResolverClass = $typeResolver->resolveFieldTypeResolverClass($fieldName)) {
                $fieldTypeResolver = $instanceManager->getInstance((string)$fieldTypeResolverClass);
                $convertedType = $fieldTypeResolver->getTypeName();
            }
        }
        // Convert the type name to standards by GraphQL
        // If "id" was converted to a Type, nothing will happen. If it was not converted, it will now be converted to "ID", for which the type has been registered
        $convertedType = self::convertTypeNameToGraphQLStandard($convertedType);

        return self::convertTypeToSDLSyntax($arrayInstances, $convertedType, $isMandatory);
    }
    public static function getFieldOrDirectiveArgTypeToOutputInSchema(string $type, ?bool $isMandatory = false): string
    {
        list (
            $arrayInstances,
            $convertedType
        ) = self::getTypeComponents($type);

        // Convert the type name to standards by GraphQL
        $convertedType = self::convertTypeNameToGraphQLStandard($convertedType);

        return self::convertTypeToSDLSyntax($arrayInstances, $convertedType, $isMandatory);
    }
    public static function convertTypeNameToGraphQLStandard(string $typeName): string
    {
        // If the type is a scalar value, we need to convert it to the official GraphQL type
        $conversionTypes = [
            // SchemaDefinition::TYPE_UNRESOLVED_ID => 'ID',
            SchemaDefinition::TYPE_ID => GraphQLSchemaDefinition::TYPE_ID,
            SchemaDefinition::TYPE_STRING => GraphQLSchemaDefinition::TYPE_STRING,
            SchemaDefinition::TYPE_INT => GraphQLSchemaDefinition::TYPE_INT,
            SchemaDefinition::TYPE_FLOAT => GraphQLSchemaDefinition::TYPE_FLOAT,
            SchemaDefinition::TYPE_BOOL => GraphQLSchemaDefinition::TYPE_BOOL,
            SchemaDefinition::TYPE_ENUM => GraphQLSchemaDefinition::TYPE_ENUM,
            SchemaDefinition::TYPE_OBJECT => GraphQLSchemaDefinition::TYPE_OBJECT,
            SchemaDefinition::TYPE_MIXED => GraphQLSchemaDefinition::TYPE_MIXED,
            SchemaDefinition::TYPE_DATE => GraphQLSchemaDefinition::TYPE_DATE,
            SchemaDefinition::TYPE_TIME => GraphQLSchemaDefinition::TYPE_TIME,
            SchemaDefinition::TYPE_URL => GraphQLSchemaDefinition::TYPE_URL,
            SchemaDefinition::TYPE_EMAIL => GraphQLSchemaDefinition::TYPE_EMAIL,
            SchemaDefinition::TYPE_IP => GraphQLSchemaDefinition::TYPE_IP,
            SchemaDefinition::TYPE_ARRAY => GraphQLSchemaDefinition::TYPE_ARRAY,
            SchemaDefinition::TYPE_INPUT_OBJECT => GraphQLSchemaDefinition::TYPE_INPUT_OBJECT,
        ];
        if (isset($conversionTypes[$typeName])) {
            $typeName = $conversionTypes[$typeName];
        }

        return $typeName;
    }
    protected static function getTypeComponents(string $type): array
    {
        $convertedType = $type;

        // Replace all instances of "array:" with wrapping the type with "[]"
        $arrayInstances = 0;
        while ($convertedType && TypeCastingHelpers::getTypeCombinationCurrentElement($convertedType) == SchemaDefinition::TYPE_ARRAY) {
            $arrayInstances++;
            $convertedType = TypeCastingHelpers::getTypeCombinationNestedElements($convertedType);
        }

        // If the type was actually only "array", without indicating its type, by now $type will be null
        // In that case, inform of the error (an array cannot have its inner type undefined)
        if (!$convertedType) {
            $translationAPI = TranslationAPIFacade::getInstance();
            throw new InvalidArgumentException(
                sprintf(
                    $translationAPI->__('Type \'%s\' doesn\'t declare the type of the innermost element'),
                    $type
                )
            );
        }

        return [
            $arrayInstances,
            $convertedType
        ];
    }
    protected static function convertTypeToSDLSyntax(int $arrayInstances, string $convertedType, ?bool $isMandatory = false): string
    {
        // Wrap the type with the array brackets
        for ($i=0; $i<$arrayInstances; $i++) {
            $convertedType = sprintf(
                '[%s]',
                $convertedType
            );
        }
        if ($isMandatory) {
            $convertedType .= '!';
        }
        return $convertedType;
    }
}
