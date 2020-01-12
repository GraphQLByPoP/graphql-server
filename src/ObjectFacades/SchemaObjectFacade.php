<?php
namespace PoP\GraphQL\ObjectFacades;

use PoP\GraphQL\ObjectModels\Schema;
use PoP\Root\Container\ContainerBuilderFactory;

class SchemaObjectFacade
{
    public static function getInstance(): Schema
    {
        $containerBuilderFactory = ContainerBuilderFactory::getInstance();
        return $containerBuilderFactory->get('schema_object');
    }
}
