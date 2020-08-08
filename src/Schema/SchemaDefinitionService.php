<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer\Schema;

use PoP\Engine\TypeResolvers\RootTypeResolver;
use GraphQLByPoP\GraphQLServer\Schema\SchemaDefinitionServiceInterface;
use PoP\ComponentModel\Facades\Instances\InstanceManagerFacade;

class SchemaDefinitionService extends \PoP\ComponentModel\Schema\SchemaDefinitionService implements SchemaDefinitionServiceInterface
{
    public function getTypeName(string $typeResolverClass): string
    {
        $instanceManager = InstanceManagerFacade::getInstance();
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

    public function getMutationTypeName(): ?string
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

    public function getSubscriptionTypeName(): ?string
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
