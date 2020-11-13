<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer;

class Environment
{
    public const ADD_SELF_FIELD_FOR_ROOT_TYPE_TO_SCHEMA = 'ADD_SELF_FIELD_FOR_ROOT_TYPE_TO_SCHEMA';
    public const SORT_SCHEMA_ALPHABETICALLY = 'SORT_SCHEMA_ALPHABETICALLY';
    public const ENABLE_REMOVE_IF_NULL_DIRECTIVE = 'ENABLE_REMOVE_IF_NULL_DIRECTIVE';
    public const ENABLE_PROACTIVE_FEEDBACK = 'ENABLE_PROACTIVE_FEEDBACK';
    public const ENABLE_NESTED_MUTATIONS = 'ENABLE_NESTED_MUTATIONS';

    public static function addGlobalFieldsToSchema(): bool
    {
        return getenv('ADD_GLOBAL_FIELDS_TO_SCHEMA') !== false ? strtolower(getenv('ADD_GLOBAL_FIELDS_TO_SCHEMA')) == "true" : false;
    }

    public static function addSelfFieldToSchema(): bool
    {
        return getenv('ADD_SELF_FIELD_TO_SCHEMA') !== false ? strtolower(getenv('ADD_SELF_FIELD_TO_SCHEMA')) == "true" : false;
    }

    public static function addFullSchemaFieldToSchema(): bool
    {
        return getenv('ADD_FULLSCHEMA_FIELD_TO_SCHEMA') !== false ? strtolower(getenv('ADD_FULLSCHEMA_FIELD_TO_SCHEMA')) == "true" : false;
    }

    public static function addVersionToSchemaFieldDescription(): bool
    {
        return getenv('ADD_VERSION_TO_SCHEMA_FIELD_DESCRIPTION') !== false ? strtolower(getenv('ADD_VERSION_TO_SCHEMA_FIELD_DESCRIPTION')) == "true" : false;
    }
}
