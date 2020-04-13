<?php
namespace PoP\GraphQL\TypeResolvers;

use PoP\ComponentModel\TypeResolvers\AbstractTypeResolver;

abstract class AbstractIntrospectionTypeResolver extends AbstractTypeResolver
{
    /**
     * Introspection fields are global, so define no namespace for them
     *
     * @return string
     */
    public function getNamespace(): string
    {
        return '';
    }
}
