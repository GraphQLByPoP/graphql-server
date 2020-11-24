<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer\Configuration;

class Request
{
    public const URLPARAM_EDIT_SCHEMA = 'edit_schema';
    public const URLPARAM_MUTATION_SCHEME = 'mutation_scheme';

    public static function editSchema(): bool
    {
        return isset($_REQUEST[self::URLPARAM_EDIT_SCHEMA]) && $_REQUEST[self::URLPARAM_EDIT_SCHEMA];
    }

    public static function getMutationScheme(): ?string
    {
        if (isset($_REQUEST[self::URLPARAM_MUTATION_SCHEME])) {
            $scheme = $_REQUEST[self::URLPARAM_MUTATION_SCHEME];
            $schemes = [
                MutationSchemes::STANDARD,
                MutationSchemes::NESTED_WITH_REDUNDANT_ROOT_FIELDS,
                MutationSchemes::NESTED_WITHOUT_REDUNDANT_ROOT_FIELDS,
            ];
            if (in_array($scheme, $schemes)) {
                return $scheme;
            }
        }
        return null;
    }
}
