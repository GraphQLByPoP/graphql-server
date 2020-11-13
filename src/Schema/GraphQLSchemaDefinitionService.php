<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer\Schema;

use PoP\Engine\TypeResolvers\RootTypeResolver;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\ComponentModel\Facades\Instances\InstanceManagerFacade;
use GraphQLByPoP\GraphQLServer\Schema\GraphQLSchemaDefinitionServiceInterface;
use PoP\ComponentModel\Schema\SchemaDefinitionService;

class GraphQLSchemaDefinitionService extends SchemaDefinitionService implements GraphQLSchemaDefinitionServiceInterface
{
    public function getTypeName(string $typeResolverClass): string
    {
        $instanceManager = InstanceManagerFacade::getInstance();
        /**
         * @var TypeResolverInterface
         */
        $typeResolver = $instanceManager->getInstance($typeResolverClass);
        return $typeResolver->getMaybeNamespacedTypeName();
    }

    public function getQueryTypeSchemaKey(): string
    {
        $instanceManager = InstanceManagerFacade::getInstance();
        $queryTypeResolverClass = $this->getQueryTypeResolverClass();
        $queryTypeResolver = $instanceManager->getInstance($queryTypeResolverClass);
        return $this->getTypeSchemaKey($queryTypeResolver);
    }

    public function getQueryTypeResolverClass(): string
    {
        return RootTypeResolver::class;
    }

    public function getMutationTypeSchemaKey(): ?string
    {
        if ($typeResolverClass = $this->getMutationTypeResolverClass()) {
            return $this->getTypeName($typeResolverClass);
        }
        return null;
    }

    public function getMutationTypeResolverClass(): ?string
    {
        return null;
    }

    public function getSubscriptionTypeSchemaKey(): ?string
    {
        if ($typeResolverClass = $this->getSubscriptionTypeResolverClass()) {
            return $this->getTypeName($typeResolverClass);
        }
        return null;
    }

    public function getSubscriptionTypeResolverClass(): ?string
    {
        return null;
    }
}
