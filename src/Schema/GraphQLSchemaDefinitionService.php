<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer\Schema;

use GraphQLByPoP\GraphQLServer\Schema\GraphQLSchemaDefinitionServiceInterface;
use PoP\Engine\Schema\SchemaDefinitionService;

class GraphQLSchemaDefinitionService extends SchemaDefinitionService implements GraphQLSchemaDefinitionServiceInterface
{
    public function getQueryTypeSchemaKey(): string
    {
        return $this->getRootTypeSchemaKey();
    }

    public function getMutationTypeSchemaKey(): ?string
    {
        return null;
        // return $this->getRootTypeSchemaKey();
    }

    public function getSubscriptionTypeSchemaKey(): ?string
    {
        return null;
    }
}
