<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer\Schema;

use PoP\Engine\Schema\SchemaDefinitionServiceInterface;

interface GraphQLSchemaDefinitionServiceInterface extends SchemaDefinitionServiceInterface
{
    public function getTypeName(string $typeResolverClass): string;
    public function getQueryTypeSchemaKey(): string;
    public function getQueryTypeResolverClass(): string;
    public function getMutationTypeSchemaKey(): ?string;
    public function getMutationTypeResolverClass(): ?string;
    public function getSubscriptionTypeSchemaKey(): ?string;
    public function getSubscriptionTypeResolverClass(): ?string;
}
