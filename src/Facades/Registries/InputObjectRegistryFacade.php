<?php
namespace PoP\GraphQL\Facades\Registries;

use PoP\GraphQL\Registries\InputObjectRegistryInterface;
use PoP\Root\Container\ContainerBuilderFactory;

class InputObjectRegistryFacade
{
    public static function getInstance(): InputObjectRegistryInterface
    {
        return ContainerBuilderFactory::getInstance()->get('input_object_registry');
    }
}
