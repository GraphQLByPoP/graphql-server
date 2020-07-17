<?php

declare(strict_types=1);

namespace PoP\GraphQL;

use PoP\ComponentModel\ComponentConfiguration\EnvironmentValueHelpers;
use PoP\ComponentModel\ComponentConfiguration\ComponentConfigurationTrait;
use PoP\GraphQLAPIQuery\ComponentConfiguration as GraphQLAPIQueryComponentConfiguration;

class ComponentConfiguration
{
    use ComponentConfigurationTrait;

    private static $addSelfFieldForRootTypeToSchema;
    private static $sortSchemaAlphabetically;

    public static function addSelfFieldForRootTypeToSchema(): bool
    {
        // By default, if enabling to pass variables as expressions for the @export directive,
        // then must add the `self` field to Root
        if (GraphQLAPIQueryComponentConfiguration::enableVariablesAsExpressions()) {
            return true;
        }

        // Define properties
        $envVariable = Environment::ADD_SELF_FIELD_FOR_ROOT_TYPE_TO_SCHEMA;
        $selfProperty = &self::$addSelfFieldForRootTypeToSchema;
        $defaultValue = false;
        $callback = [EnvironmentValueHelpers::class, 'toBool'];

        // Initialize property from the environment/hook
        self::maybeInitializeConfigurationValue(
            $envVariable,
            $selfProperty,
            $defaultValue,
            $callback
        );
        return $selfProperty;
    }

    public static function sortSchemaAlphabetically(): bool
    {
        // Define properties
        $envVariable = Environment::SORT_SCHEMA_ALPHABETICALLY;
        $selfProperty = &self::$sortSchemaAlphabetically;
        $defaultValue = true;
        $callback = [EnvironmentValueHelpers::class, 'toBool'];

        // Initialize property from the environment/hook
        self::maybeInitializeConfigurationValue(
            $envVariable,
            $selfProperty,
            $defaultValue,
            $callback
        );
        return $selfProperty;
    }
}
