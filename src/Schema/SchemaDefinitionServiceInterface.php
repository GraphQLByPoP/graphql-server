<?php
namespace PoP\GraphQL\Schema;

interface SchemaDefinitionServiceInterface extends \PoP\ComponentModel\Schema\SchemaDefinitionServiceInterface
{
    public function getTypeName(string $typeResolverClass): string;
    public function getQueryTypeSchemaKey(): string;
    public function getQueryTypeResolverClass(): string;
    public function getMutationTypeName(): ?string;
    public function getMutationTypeResolverClass(): ?string;
    public function getSubscriptionTypeName(): ?string;
    public function getSubscriptionTypeResolverClass(): ?string;
}
