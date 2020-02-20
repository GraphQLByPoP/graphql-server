<?php
namespace PoP\GraphQL;

class Environment
{
    public static function addGlobalFieldsToSchema(): bool
    {
        return isset($_ENV['ADD_GLOBAL_FIELDS_TO_SCHEMA']) ? strtolower($_ENV['ADD_GLOBAL_FIELDS_TO_SCHEMA']) == "true" : false;
    }

    public static function addSystemDirectivesToSchema(): bool
    {
        return isset($_ENV['ADD_SYSTEM_DIRECTIVES_TO_SCHEMA']) ? strtolower($_ENV['ADD_SYSTEM_DIRECTIVES_TO_SCHEMA']) == "true" : false;
    }

    public static function addExtendedGraphQLDirectivesToSchema(): bool
    {
        return isset($_ENV['ADD_EXTENDED_GRAPHQL_DIRECTIVES_TO_SCHEMA']) ? strtolower($_ENV['ADD_EXTENDED_GRAPHQL_DIRECTIVES_TO_SCHEMA']) == "true" : false;
    }
}

