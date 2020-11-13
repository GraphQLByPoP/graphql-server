<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer\Config;

use PoP\Root\Component\PHPServiceConfigurationTrait;
use PoP\ComponentModel\Container\ContainerBuilderUtils;
use PoP\ComponentModel\DataStructure\DataStructureManagerInterface;
use PoP\ModuleRouting\RouteModuleProcessorManagerInterface;

class ServiceConfiguration
{
    use PHPServiceConfigurationTrait;

    protected static function configure(): void
    {
        /**
         * Override class GraphQLDataStructureFormatter from GraphQLAPI
         */
        ContainerBuilderUtils::injectServicesIntoService(
            DataStructureManagerInterface::class,
            'GraphQLByPoP\\GraphQLServer\\DataStructureFormatters',
            'add'
        );

        // Add RouteModuleProcessors to the Manager
        ContainerBuilderUtils::injectServicesIntoService(
            RouteModuleProcessorManagerInterface::class,
            'GraphQLByPoP\\GraphQLServer\\RouteModuleProcessors',
            'add'
        );
    }
}
