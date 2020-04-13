<?php
namespace PoP\GraphQL;

class Environment
{
    public const ADD_SELF_FIELD_FOR_ROOT_TYPE_TO_SCHEMA = 'ADD_SELF_FIELD_FOR_ROOT_TYPE_TO_SCHEMA';

    public static function addGlobalFieldsToSchema(): bool
    {
        return isset($_ENV['ADD_GLOBAL_FIELDS_TO_SCHEMA']) ? strtolower($_ENV['ADD_GLOBAL_FIELDS_TO_SCHEMA']) == "true" : false;
    }

    public static function addSelfFieldToSchema(): bool
    {
        return isset($_ENV['ADD_SELF_FIELD_TO_SCHEMA']) ? strtolower($_ENV['ADD_SELF_FIELD_TO_SCHEMA']) == "true" : false;
    }

    public static function addSelfFieldForRootTypeToSchema(): bool
    {
        return isset($_ENV[self::ADD_SELF_FIELD_FOR_ROOT_TYPE_TO_SCHEMA]) ? strtolower($_ENV[self::ADD_SELF_FIELD_FOR_ROOT_TYPE_TO_SCHEMA]) == "true" : false;
    }

    public static function addFullSchemaFieldToSchema(): bool
    {
        return isset($_ENV['ADD_FULLSCHEMA_FIELD_TO_SCHEMA']) ? strtolower($_ENV['ADD_FULLSCHEMA_FIELD_TO_SCHEMA']) == "true" : false;
    }

    public static function addVersionToSchemaFieldDescription(): bool
    {
        return isset($_ENV['ADD_VERSION_TO_SCHEMA_FIELD_DESCRIPTION']) ? strtolower($_ENV['ADD_VERSION_TO_SCHEMA_FIELD_DESCRIPTION']) == "true" : false;
    }
}
