<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer\Schema;

use PoP\Engine\Schema\SchemaDefinitionServiceInterface;

interface GraphQLSchemaDefinitionServiceInterface extends SchemaDefinitionServiceInterface
{
    public function getQueryTypeSchemaKey(): string;
    public function getQueryRootTypeResolverClass(): string;
    public function getMutationTypeSchemaKey(): ?string;
    public function getMutationRootTypeResolverClass(): ?string;
    public function getSubscriptionTypeSchemaKey(): ?string;
    public function getSubscriptionRootTypeResolverClass(): ?string;
}
