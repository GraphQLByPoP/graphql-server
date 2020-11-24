<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer\Hooks;

use GraphQLByPoP\GraphQLServer\ComponentConfiguration;
use PoP\Hooks\AbstractHookSet;
use PoP\ComponentModel\TypeResolvers\HookHelpers;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\ComponentModel\FieldResolvers\FieldResolverInterface;
use GraphQLByPoP\GraphQLServer\Facades\Schema\GraphQLSchemaDefinitionServiceFacade;

class NestedMutationHooks extends AbstractHookSet
{
    protected function init()
    {
        $this->hooksAPI->addFilter(
            HookHelpers::getHookNameToFilterField(),
            array($this, 'maybeFilterFieldName'),
            10,
            5
        );
    }

    /**
     * If nested mutations are disabled, then remove registering fieldNames
     * when they have a MutationResolver for types other than the Root and MutationRoot
     */
    public function maybeFilterFieldName(
        bool $include,
        TypeResolverInterface $typeResolver,
        FieldResolverInterface $fieldResolver,
        array $fieldInterfaceResolverClasses,
        string $fieldName
    ): bool {
        if (ComponentConfiguration::enableNestedMutations()) {
            return $include;
        }
        $graphQLSchemaDefinitionService = GraphQLSchemaDefinitionServiceFacade::getInstance();
        if (
            $include
            && !in_array(get_class($typeResolver), [
                $graphQLSchemaDefinitionService->getRootTypeResolverClass(),
                $graphQLSchemaDefinitionService->getMutationRootTypeResolverClass(),
            ])
            && $fieldResolver->resolveFieldMutationResolverClass($typeResolver, $fieldName) !== null
        ) {
            return false;
        }

        return $include;
    }
}
