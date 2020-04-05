<?php
namespace PoP\GraphQL\Configuration;

class Request
{
    const URLPARAM_EDIT_SCHEMA = 'edit_schema';
    public static function editSchema(): bool
    {
        return isset($_REQUEST[self::URLPARAM_EDIT_SCHEMA]) && $_REQUEST[self::URLPARAM_EDIT_SCHEMA];
    }
}

