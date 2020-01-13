<?php
namespace PoP\GraphQL\Facades\Registries;

use PoP\GraphQL\Registries\InterfaceRegistryInterface;
use PoP\Root\Container\ContainerBuilderFactory;

class InterfaceRegistryFacade
{
    public static function getInstance(): InterfaceRegistryInterface
    {
        return ContainerBuilderFactory::getInstance()->get('interface_registry');
    }
}
