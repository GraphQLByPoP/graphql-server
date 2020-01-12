<?php
namespace PoP\GraphQL\TypeDataLoaders;

use PoP\GraphQL\ObjectFacades\SchemaObjectFacade;
use PoP\ComponentModel\TypeDataLoaders\AbstractTypeDataLoader;

class SchemaTypeDataLoader extends AbstractTypeDataLoader
{
    public function getObjects(array $ids): array
    {
        return [SchemaObjectFacade::getInstance()];
    }
}
