<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer\Configuration;

class Request
{
    public const URLPARAM_EDIT_SCHEMA = 'edit_schema';
    public const URLPARAM_MUTATION_SCHEME = 'mutation_scheme';
    public const URLPARAM_VALUE_MUTATION_SCHEME_STANDARD = 'standard';
    public const URLPARAM_VALUE_MUTATION_SCHEME_NESTED_WITH_REDUNDANT_ROOT_FIELDS = 'nested';
    public const URLPARAM_VALUE_MUTATION_SCHEME_NESTED_WITHOUT_REDUNDANT_ROOT_FIELDS = 'lean_nested';

    public static function editSchema(): bool
    {
        return isset($_REQUEST[self::URLPARAM_EDIT_SCHEMA]) && $_REQUEST[self::URLPARAM_EDIT_SCHEMA];
    }

    public static function getMutationScheme(): ?string
    {
        if (isset($_REQUEST[self::URLPARAM_MUTATION_SCHEME])) {
            $scheme = $_REQUEST[self::URLPARAM_MUTATION_SCHEME];
            $schemes = [
                self::URLPARAM_VALUE_MUTATION_SCHEME_STANDARD,
                self::URLPARAM_VALUE_MUTATION_SCHEME_NESTED_WITH_REDUNDANT_ROOT_FIELDS,
                self::URLPARAM_VALUE_MUTATION_SCHEME_NESTED_WITHOUT_REDUNDANT_ROOT_FIELDS,
            ];
            if (in_array($scheme, $schemes)) {
                return $scheme;
            }
        }
        return null;
    }
}
