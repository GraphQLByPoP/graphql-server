<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer\Hooks;

use GraphQLByPoP\GraphQLQuery\Environment as GraphQLAPIQueryEnvironment;
use PoP\API\Environment as APIEnvironment;
use GraphQLByPoP\GraphQLQuery\ComponentConfiguration as GraphQLAPIQueryComponentConfiguration;
use PoP\API\ComponentConfiguration as APIComponentConfiguration;
use PoP\Engine\Hooks\AbstractHookSet;
use PoP\ComponentModel\ComponentConfiguration\ComponentConfigurationHelpers;

class ComponentConfigurationHookSet extends AbstractHookSet
{
    protected function init()
    {
        /**
         * Set environment variable to true because it's needed by @export
         */
        $hookName = ComponentConfigurationHelpers::getHookName(
            GraphQLAPIQueryComponentConfiguration::class,
            GraphQLAPIQueryEnvironment::ENABLE_VARIABLES_AS_EXPRESSIONS
        );
        $this->hooksAPI->addFilter(
            $hookName,
            function () {
                return true;
            }
        );
        /**
         * @export requires the queries to be executed in order
         */
        $hookName = ComponentConfigurationHelpers::getHookName(
            APIComponentConfiguration::class,
            APIEnvironment::EXECUTE_QUERY_BATCH_IN_STRICT_ORDER
        );
        $this->hooksAPI->addFilter(
            $hookName,
            function () {
                return true;
            }
        );
    }
}
