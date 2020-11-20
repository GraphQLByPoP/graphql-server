<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer\Hooks;

use GraphQLByPoP\GraphQLQuery\Environment as GraphQLQueryEnvironment;
use GraphQLByPoP\GraphQLServer\Environment as GraphQLServerEnvironment;
use PoP\Engine\Environment as EngineEnvironment;
use PoP\API\Environment as APIEnvironment;
use GraphQLByPoP\GraphQLQuery\ComponentConfiguration as GraphQLQueryComponentConfiguration;
use GraphQLByPoP\GraphQLRequest\ComponentConfiguration as GraphQLRequestComponentConfiguration;
use GraphQLByPoP\GraphQLServer\ComponentConfiguration as GraphQLServerComponentConfiguration;
use PoP\Engine\ComponentConfiguration as EngineComponentConfiguration;
use PoP\API\ComponentConfiguration as APIComponentConfiguration;
use PoP\Hooks\AbstractHookSet;
use PoP\ComponentModel\ComponentConfiguration\ComponentConfigurationHelpers;
use GraphQLByPoP\GraphQLServer\Configuration\Request;

class ComponentConfigurationHookSet extends AbstractHookSet
{
    protected function init()
    {
        if (GraphQLRequestComponentConfiguration::enableMultipleQueryExecution()) {
            /**
             * Set environment variable to true because it's needed by @export
             */
            $hookName = ComponentConfigurationHelpers::getHookName(
                GraphQLQueryComponentConfiguration::class,
                GraphQLQueryEnvironment::ENABLE_VARIABLES_AS_EXPRESSIONS
            );
            $this->hooksAPI->addFilter(
                $hookName,
                fn () => true
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
                fn () => true
            );
        }

        // The mutation scheme can be set by param ?mutation_scheme=..., with values:
        // - "standard" => Use QueryRoot and MutationRoot
        // - "nested" => Use Root, and nested mutations with redundant root fields
        // - "lean_nested" => Use Root, and nested mutations without redundant root fields
        if ($mutationScheme = Request::getMutationsScheme()) {
            if ($mutationScheme == Request::URLPARAM_VALUE_MUTATION_SCHEME_STANDARD) {
                $hookName = ComponentConfigurationHelpers::getHookName(
                    GraphQLServerComponentConfiguration::class,
                    GraphQLServerEnvironment::ENABLE_NESTED_MUTATIONS
                );
                $this->hooksAPI->addFilter(
                    $hookName,
                    fn () => false
                );
            } elseif (
                $mutationScheme == Request::URLPARAM_VALUE_MUTATION_SCHEME_NESTED_WITH_REDUNDANT_ROOT_FIELDS
                || $mutationScheme == Request::URLPARAM_VALUE_MUTATION_SCHEME_NESTED_WITHOUT_REDUNDANT_ROOT_FIELDS
            ) {
                $hookName = ComponentConfigurationHelpers::getHookName(
                    GraphQLServerComponentConfiguration::class,
                    GraphQLServerEnvironment::ENABLE_NESTED_MUTATIONS
                );
                $this->hooksAPI->addFilter(
                    $hookName,
                    fn () => true
                );
                $hookName = ComponentConfigurationHelpers::getHookName(
                    EngineComponentConfiguration::class,
                    EngineEnvironment::DISABLE_REDUNDANT_ROOT_TYPE_MUTATION_FIELDS
                );
                $this->hooksAPI->addFilter(
                    $hookName,
                    fn () => $mutationScheme == Request::URLPARAM_VALUE_MUTATION_SCHEME_NESTED_WITHOUT_REDUNDANT_ROOT_FIELDS
                );
            }
        }
    }
}
