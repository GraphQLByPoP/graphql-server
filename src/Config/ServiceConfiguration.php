<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer\Config;

use PoP\Root\Component\PHPServiceConfigurationTrait;
use PoP\ComponentModel\Container\ContainerBuilderUtils;
use PoP\ComponentModel\DataStructure\DataStructureManagerInterface;

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
    }
}
