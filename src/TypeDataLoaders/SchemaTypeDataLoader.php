<?php
namespace PoP\GraphQL\TypeDataLoaders;

use PoP\ComponentModel\Facades\Container\ObjectDictionaryFacade;
use PoP\ComponentModel\TypeDataLoaders\AbstractTypeDataLoader;
use PoP\GraphQL\ObjectModels\Schema;
use PoP\GraphQL\TypeResolvers\SchemaTypeResolver;

class SchemaTypeDataLoader extends AbstractTypeDataLoader
{
    public function getObjects(array $ids): array
    {
        $objectDictionary = ObjectDictionaryFacade::getInstance();
        $ret = [];
        foreach ($ids as $id) {
            if (!$objectDictionary->has(SchemaTypeResolver::class, $id)) {
                $schema = new Schema($id);
                $objectDictionary->set(SchemaTypeResolver::class, $id, $schema);
            }
            $ret[] = $objectDictionary->get(SchemaTypeResolver::class, $id);
        }
        return $ret;
    }
}
