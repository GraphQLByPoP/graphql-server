<?php
namespace PoP\GraphQL;

class Environment
{
    public static function addGlobalFieldsToSchema(): bool
    {
        return isset($_ENV['ADD_GLOBAL_FIELDS_TO_SCHEMA']) ? strtolower($_ENV['ADD_GLOBAL_FIELDS_TO_SCHEMA']) == "true" : false;
    }
}

