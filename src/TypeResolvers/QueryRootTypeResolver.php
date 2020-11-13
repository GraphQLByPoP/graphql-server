<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer\TypeResolvers;

use PoP\Engine\TypeResolvers\ReservedNameTypeResolverTrait;
use GraphQLByPoP\GraphQLServer\ObjectModels\QueryRoot;
use GraphQLByPoP\GraphQLServer\TypeDataLoaders\QueryRootTypeDataLoader;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\TypeResolvers\AbstractTypeResolver;

class QueryRootTypeResolver extends AbstractTypeResolver
{
    use ReservedNameTypeResolverTrait;

    public const NAME = 'QueryRoot';

    public function getTypeName(): string
    {
        return self::NAME;
    }

    public function getSchemaTypeDescription(): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        return $translationAPI->__('Query type, starting from which the query is executed', 'graphql-server');
    }

    public function getID(object $resultItem)
    {
        /** @var QueryRoot */
        $queryRoot = $resultItem;
        return $queryRoot->getID();
    }

    public function getTypeDataLoaderClass(): string
    {
        return QueryRootTypeDataLoader::class;
    }
}
