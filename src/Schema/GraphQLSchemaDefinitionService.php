<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer\Schema;

use PoP\Engine\Schema\SchemaDefinitionService;
use GraphQLByPoP\GraphQLServer\ComponentConfiguration;
use PoP\ComponentModel\Facades\Instances\InstanceManagerFacade;
use GraphQLByPoP\GraphQLServer\TypeResolvers\QueryRootTypeResolver;
use GraphQLByPoP\GraphQLServer\TypeResolvers\MutationRootTypeResolver;
use GraphQLByPoP\GraphQLServer\Schema\GraphQLSchemaDefinitionServiceInterface;

class GraphQLSchemaDefinitionService extends SchemaDefinitionService implements GraphQLSchemaDefinitionServiceInterface
{
    public function getQueryTypeSchemaKey(): string
    {
        $instanceManager = InstanceManagerFacade::getInstance();
        $queryTypeResolverClass = $this->getQueryRootTypeResolverClass();
        $queryTypeResolver = $instanceManager->getInstance($queryTypeResolverClass);
        return $this->getTypeSchemaKey($queryTypeResolver);
    }

    /**
     * If nested mutations are enabled, use "Root".
     * Otherwise, use "Query"
     */
    public function getQueryRootTypeResolverClass(): string
    {
        if (ComponentConfiguration::enableNestedMutations()) {
            return $this->getRootTypeResolverClass();
        }
        return QueryRootTypeResolver::class;
    }

    public function getMutationTypeSchemaKey(): ?string
    {
        if ($mutationTypeResolverClass = $this->getMutationRootTypeResolverClass()) {
            $instanceManager = InstanceManagerFacade::getInstance();
            $mutationTypeResolver = $instanceManager->getInstance($mutationTypeResolverClass);
            return $this->getTypeSchemaKey($mutationTypeResolver);
        }
        return null;
    }

    /**
     * If nested mutations are enabled, use "Root".
     * Otherwise, use "Mutation"
     */
    public function getMutationRootTypeResolverClass(): ?string
    {
        if (ComponentConfiguration::enableNestedMutations()) {
            return $this->getRootTypeResolverClass();
        }
        return MutationRootTypeResolver::class;
    }

    public function getSubscriptionTypeSchemaKey(): ?string
    {
        if ($subscriptionTypeResolverClass = $this->getSubscriptionRootTypeResolverClass()) {
            $instanceManager = InstanceManagerFacade::getInstance();
            $subscriptionTypeResolver = $instanceManager->getInstance($subscriptionTypeResolverClass);
            return $this->getTypeSchemaKey($subscriptionTypeResolver);
        }
        return null;
    }

    /**
     * Not yet implemented
     */
    public function getSubscriptionRootTypeResolverClass(): ?string
    {
        return null;
    }
}
