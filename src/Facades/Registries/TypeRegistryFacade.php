<?php
namespace PoP\GraphQL\Facades\Registries;

use PoP\GraphQL\Registries\TypeRegistryInterface;
use PoP\Root\Container\ContainerBuilderFactory;

class TypeRegistryFacade
{
    public static function getInstance(): TypeRegistryInterface
    {
        return ContainerBuilderFactory::getInstance()->get('type_registry');
    }
}
