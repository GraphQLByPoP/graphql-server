<?php

declare(strict_types=1);

namespace PoP\GraphQL\Config;

use PoP\Root\Component\PHPServiceConfigurationTrait;
use PoP\ComponentModel\Container\ContainerBuilderUtils;

class ServiceConfiguration
{
    use PHPServiceConfigurationTrait;

    protected static function configure()
    {
        /**
         * Override class GraphQLDataStructureFormatter from GraphQLAPI
         */
        ContainerBuilderUtils::injectServicesIntoService(
            'data_structure_manager',
            'PoP\\GraphQL\\DataStructureFormatters',
            'add'
        );
    }
}
