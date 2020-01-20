<?php
namespace PoP\GraphQL\Schema;

use PoP\API\TypeResolvers\RootTypeResolver;
use PoP\GraphQL\Schema\SchemaDefinitionServiceInterface;
use PoP\ComponentModel\Facades\Instances\InstanceManagerFacade;

class SchemaDefinitionService extends \PoP\ComponentModel\Schema\SchemaDefinitionService implements SchemaDefinitionServiceInterface
{
    public function getTypeName(string $typeResolverClass): string
    {
        $instanceManager = InstanceManagerFacade::getInstance();
        $typeResolver = $instanceManager->getInstance($typeResolverClass);
        return $typeResolver->getTypeName();
    }

    public function getQueryTypeName(): string
    {
        return $this->getTypeName($this->getQueryTypeResolverClass());
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
