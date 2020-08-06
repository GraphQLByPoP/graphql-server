<?php

declare(strict_types=1);

namespace PoP\GraphQL\Hooks;

use PoP\GraphQLAPIQuery\Environment;
use PoP\GraphQLAPIQuery\ComponentConfiguration;
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
            ComponentConfiguration::class,
            Environment::ENABLE_VARIABLES_AS_EXPRESSIONS
        );
        $this->hooksAPI->addFilter(
            $hookName,
            function () {
                return true;
            }
        );
    }
}
