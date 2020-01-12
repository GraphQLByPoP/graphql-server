<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\API\TypeResolvers\RootTypeResolver;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\ComponentModel\Facades\Instances\InstanceManagerFacade;

class Schema
{
    private $id;
    public function __construct(string $id)
    {
        $this->id = $id;
    }
    public function getID() {
        return $this->id;
    }
    public function getQueryTypeResolverInstance(): TypeResolverInterface
    {
        $instanceManager = InstanceManagerFacade::getInstance();
        return $instanceManager->getInstance(RootTypeResolver::class);
    }
    public function getMutationTypeResolverInstance(): ?TypeResolverInterface
    {
        return null;
    }
    public function getSubscriptionTypeResolverInstance(): ?TypeResolverInterface
    {
        return null;
    }
}
