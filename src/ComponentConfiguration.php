<?php

declare(strict_types=1);

namespace PoP\GraphQL;

use PoP\ComponentModel\AbstractComponentConfiguration;
use PoP\GraphQLAPIQuery\ComponentConfiguration as GraphQLAPIQueryComponentConfiguration;

class ComponentConfiguration extends AbstractComponentConfiguration
{
    private static $addSelfFieldForRootTypeToSchema;

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
        $callback = [Environment::class, 'addSelfFieldForRootTypeToSchema'];

        // Initialize property from the environment/hook
        self::maybeInitEnvironmentVariable(
            $envVariable,
            $selfProperty,
            $callback
        );
        return $selfProperty;
    }
}
