<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer\Facades\Registries;

use GraphQLByPoP\GraphQLServer\Registries\SchemaDefinitionReferenceRegistryInterface;
use PoP\Root\Container\ContainerBuilderFactory;

class SchemaDefinitionReferenceRegistryFacade
{
    public static function getInstance(): SchemaDefinitionReferenceRegistryInterface
    {
        /**
         * @var SchemaDefinitionReferenceRegistryInterface
         */
        $service = ContainerBuilderFactory::getInstance()->get('schema_definition_reference_registry');
        return $service;
    }
}
