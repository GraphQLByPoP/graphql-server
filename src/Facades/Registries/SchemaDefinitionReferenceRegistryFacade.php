<?php

declare(strict_types=1);

namespace PoP\GraphQLServer\Facades\Registries;

use PoP\GraphQLServer\Registries\SchemaDefinitionReferenceRegistryInterface;
use PoP\Root\Container\ContainerBuilderFactory;

class SchemaDefinitionReferenceRegistryFacade
{
    public static function getInstance(): SchemaDefinitionReferenceRegistryInterface
    {
        return ContainerBuilderFactory::getInstance()->get('schema_definition_reference_registry');
    }
}
