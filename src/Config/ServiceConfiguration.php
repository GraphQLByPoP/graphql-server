<?php

declare(strict_types=1);

namespace PoP\GraphQLServer\Config;

use PoP\Root\Component\PHPServiceConfigurationTrait;
use PoP\ComponentModel\Container\ContainerBuilderUtils;

class ServiceConfiguration
{
    use PHPServiceConfigurationTrait;

    protected static function configure(): void
    {
        /**
         * Override class GraphQLDataStructureFormatter from GraphQLAPI
         */
        ContainerBuilderUtils::injectServicesIntoService(
            'data_structure_manager',
            'PoP\\GraphQLServer\\DataStructureFormatters',
            'add'
        );
    }
}
