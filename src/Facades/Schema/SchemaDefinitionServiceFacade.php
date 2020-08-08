<?php

declare(strict_types=1);

namespace PoP\GraphQLServer\Facades\Schema;

use PoP\GraphQLServer\Schema\SchemaDefinitionServiceInterface;
use PoP\Root\Container\ContainerBuilderFactory;

class SchemaDefinitionServiceFacade
{
    public static function getInstance(): SchemaDefinitionServiceInterface
    {
        return ContainerBuilderFactory::getInstance()->get('schema_definition_service');
    }
}
