<?php
namespace PoP\GraphQL\Facades\Registries;

use PoP\GraphQL\Registries\SchemaDefinitionReferenceRegistryInterface;
use PoP\Root\Container\ContainerBuilderFactory;

class SchemaDefinitionReferenceRegistryFacade
{
    public static function getInstance(): SchemaDefinitionReferenceRegistryInterface
    {
        return ContainerBuilderFactory::getInstance()->get('schema_definition_reference_registry');
    }
}
