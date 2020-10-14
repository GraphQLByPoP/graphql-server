<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer\Schema;

use PoP\ComponentModel\Schema\SchemaDefinitionServiceInterface;

interface GraphQLSchemaDefinitionServiceInterface extends SchemaDefinitionServiceInterface
{
    public function getTypeName(string $typeResolverClass): string;
    public function getQueryTypeSchemaKey(): string;
    public function getQueryTypeResolverClass(): string;
    public function getMutationTypeName(): ?string;
    public function getMutationTypeResolverClass(): ?string;
    public function getSubscriptionTypeName(): ?string;
    public function getSubscriptionTypeResolverClass(): ?string;
}
