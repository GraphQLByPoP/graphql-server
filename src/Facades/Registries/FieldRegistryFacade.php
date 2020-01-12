<?php
namespace PoP\GraphQL\Facades\Registries;

use PoP\GraphQL\Registries\FieldRegistryInterface;
use PoP\Root\Container\ContainerBuilderFactory;

class FieldRegistryFacade
{
    public static function getInstance(): FieldRegistryInterface
    {
        return ContainerBuilderFactory::getInstance()->get('field_registry');
    }
}
