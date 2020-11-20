<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer\Hooks;

use PoP\API\Cache\CacheUtils;
use PoP\Hooks\AbstractHookSet;
use GraphQLByPoP\GraphQLServer\ComponentConfiguration;

class SchemaCacheHooks extends AbstractHookSet
{
    protected function init()
    {
        $this->hooksAPI->addFilter(
            CacheUtils::HOOK_SCHEMA_CACHE_KEY_COMPONENTS,
            array($this, 'getSchemaCacheKeyComponents')
        );
    }

    public function getSchemaCacheKeyComponents(array $components): array
    {
        $components['nested-mutations-enabled'] = ComponentConfiguration::enableNestedMutations();
        return $components;
    }
}
