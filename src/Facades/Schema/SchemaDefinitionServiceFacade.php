<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer\Facades\Schema;

use GraphQLByPoP\GraphQLServer\Schema\SchemaDefinitionServiceInterface;
use PoP\Root\Container\ContainerBuilderFactory;

class SchemaDefinitionServiceFacade
{
    public static function getInstance(): SchemaDefinitionServiceInterface
    {
        return ContainerBuilderFactory::getInstance()->get('schema_definition_service');
    }
}
