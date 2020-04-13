<?php

declare(strict_types=1);

namespace PoP\GraphQL\Facades\Schema;

use PoP\GraphQL\Schema\SchemaDefinitionServiceInterface;
use PoP\Root\Container\ContainerBuilderFactory;

class SchemaDefinitionServiceFacade
{
    public static function getInstance(): SchemaDefinitionServiceInterface
    {
        return ContainerBuilderFactory::getInstance()->get('schema_definition_service');
    }
}
