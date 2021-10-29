<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer\Schema;

use GraphQLByPoP\GraphQLServer\TypeResolvers\ObjectType\MutationRootObjectTypeResolver;
use GraphQLByPoP\GraphQLServer\TypeResolvers\ObjectType\QueryRootObjectTypeResolver;
use PoP\API\ComponentConfiguration as APIComponentConfiguration;
use PoP\ComponentModel\State\ApplicationState;
use PoP\ComponentModel\TypeResolvers\ObjectType\ObjectTypeResolverInterface;
use PoP\API\Schema\SchemaDefinitionService;
use Symfony\Contracts\Service\Attribute\Required;

class GraphQLSchemaDefinitionService extends SchemaDefinitionService implements GraphQLSchemaDefinitionServiceInterface
{
    private ?QueryRootObjectTypeResolver $queryRootObjectTypeResolver = null;
    private ?MutationRootObjectTypeResolver $mutationRootObjectTypeResolver = null;

    public function setQueryRootObjectTypeResolver(QueryRootObjectTypeResolver $queryRootObjectTypeResolver): void
    {
        $this->queryRootObjectTypeResolver = $queryRootObjectTypeResolver;
    }
    protected function getQueryRootObjectTypeResolver(): QueryRootObjectTypeResolver
    {
        return $this->queryRootObjectTypeResolver ??= $this->instanceManager->getInstance(QueryRootObjectTypeResolver::class);
    }
    public function setMutationRootObjectTypeResolver(MutationRootObjectTypeResolver $mutationRootObjectTypeResolver): void
    {
        $this->mutationRootObjectTypeResolver = $mutationRootObjectTypeResolver;
    }
    protected function getMutationRootObjectTypeResolver(): MutationRootObjectTypeResolver
    {
        return $this->mutationRootObjectTypeResolver ??= $this->instanceManager->getInstance(MutationRootObjectTypeResolver::class);
    }

    //#[Required]
    final public function autowireGraphQLSchemaDefinitionService(QueryRootObjectTypeResolver $queryRootObjectTypeResolver, MutationRootObjectTypeResolver $mutationRootObjectTypeResolver): void
    {
        $this->queryRootObjectTypeResolver = $queryRootObjectTypeResolver;
        $this->mutationRootObjectTypeResolver = $mutationRootObjectTypeResolver;
    }

    /**
     * If nested mutations are enabled, use "Root".
     * Otherwise, use "Query"
     */
    public function getSchemaQueryRootObjectTypeResolver(): ObjectTypeResolverInterface
    {
        $vars = ApplicationState::getVars();
        if ($vars['nested-mutations-enabled']) {
            return $this->getSchemaRootObjectTypeResolver();
        }

        return $this->getQueryRootObjectTypeResolver();
    }

    /**
     * If nested mutations are enabled, use "Root".
     * Otherwise, use "Mutation"
     */
    public function getSchemaMutationRootObjectTypeResolver(): ?ObjectTypeResolverInterface
    {
        if (!APIComponentConfiguration::enableMutations()) {
            return null;
        }
        $vars = ApplicationState::getVars();
        if ($vars['nested-mutations-enabled']) {
            return $this->getSchemaRootObjectTypeResolver();
        }

        return $this->getMutationRootObjectTypeResolver();
    }

    /**
     * @todo Implement
     */
    public function getSchemaSubscriptionRootTypeResolver(): ?ObjectTypeResolverInterface
    {
        return null;
    }
}
