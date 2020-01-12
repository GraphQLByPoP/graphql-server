<?php
namespace PoP\GraphQL\TypeDataLoaders;

use PoP\GraphQL\ObjectModels\Type;
use PoP\ComponentModel\TypeDataLoaders\AbstractTypeDataLoader;

class TypeTypeDataLoader extends AbstractTypeDataLoader
{
    public function getObjects(array $ids): array
    {
        // Currently it deals only with the current site and nothing else
        $ret = [];
        foreach ($ids as $id) {
            $ret[] = new Type($id);
        }
        return $ret;
    }
}
