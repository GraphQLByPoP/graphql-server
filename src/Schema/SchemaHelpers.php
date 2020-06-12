<?php

declare(strict_types=1);

namespace PoP\GraphQL\Schema;

use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\GraphQL\Schema\SchemaDefinition as GraphQLSchemaDefinition;
use PoP\ComponentModel\Schema\SchemaHelpers as ComponentModelSchemaHelpers;

class SchemaHelpers
{
    /**
     * Convert the field type from its internal representation (eg: "array:id")
     * to the GraphQL standard representation (eg: "[Post]")
     *
     * If $isNonNullableOrMandatory is `true`, a "!" is added to the type name,
     * to handle both field response and field arguments:
     *
     * - field response: isNonNullable
     * - field argument: isMandatory (its provided value can still be null)
     *
     * @param string $type
     * @param boolean|null $isNonNullableOrMandatory
     * @return string
     */
    public static function getTypeToOutputInSchema(string $type, ?bool $isNonNullableOrMandatory = false): string
    {
        list (
            $arrayInstances,
            $convertedType
        ) = ComponentModelSchemaHelpers::getTypeComponents($type);

        // Convert the type name to standards by GraphQL
        $convertedType = self::convertTypeNameToGraphQLStandard($convertedType);

        return self::convertTypeToSDLSyntax($arrayInstances, $convertedType, $isNonNullableOrMandatory);
    }
    public static function convertTypeNameToGraphQLStandard(string $typeName): string
    {
        // If the type is a scalar value, we need to convert it to the official GraphQL type
        $conversionTypes = [
            SchemaDefinition::TYPE_ID => GraphQLSchemaDefinition::TYPE_ID,
            SchemaDefinition::TYPE_STRING => GraphQLSchemaDefinition::TYPE_STRING,
            SchemaDefinition::TYPE_INT => GraphQLSchemaDefinition::TYPE_INT,
            SchemaDefinition::TYPE_FLOAT => GraphQLSchemaDefinition::TYPE_FLOAT,
            SchemaDefinition::TYPE_BOOL => GraphQLSchemaDefinition::TYPE_BOOL,
            SchemaDefinition::TYPE_OBJECT => GraphQLSchemaDefinition::TYPE_OBJECT,
            SchemaDefinition::TYPE_MIXED => GraphQLSchemaDefinition::TYPE_MIXED,
            SchemaDefinition::TYPE_DATE => GraphQLSchemaDefinition::TYPE_DATE,
            SchemaDefinition::TYPE_TIME => GraphQLSchemaDefinition::TYPE_TIME,
            SchemaDefinition::TYPE_URL => GraphQLSchemaDefinition::TYPE_URL,
            SchemaDefinition::TYPE_EMAIL => GraphQLSchemaDefinition::TYPE_EMAIL,
            SchemaDefinition::TYPE_IP => GraphQLSchemaDefinition::TYPE_IP,
            SchemaDefinition::TYPE_ENUM => GraphQLSchemaDefinition::TYPE_ENUM,
            SchemaDefinition::TYPE_ARRAY => GraphQLSchemaDefinition::TYPE_ARRAY,
            SchemaDefinition::TYPE_INPUT_OBJECT => GraphQLSchemaDefinition::TYPE_INPUT_OBJECT,
        ];
        if (isset($conversionTypes[$typeName])) {
            $typeName = $conversionTypes[$typeName];
        }

        return $typeName;
    }
    protected static function convertTypeToSDLSyntax(int $arrayInstances, string $convertedType, ?bool $isNonNullableOrMandatory = false): string
    {
        // Wrap the type with the array brackets
        for ($i = 0; $i < $arrayInstances; $i++) {
            $convertedType = sprintf(
                '[%s]',
                $convertedType
            );
        }
        if ($isNonNullableOrMandatory) {
            $convertedType .= '!';
        }
        return $convertedType;
    }
}
