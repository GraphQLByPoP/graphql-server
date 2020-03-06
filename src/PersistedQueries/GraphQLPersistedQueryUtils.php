<?php
namespace PoP\GraphQL\PersistedQueries;

use PoP\API\Facades\PersistedQueryManagerFacade;
use PoP\API\Facades\PersistedFragmentManagerFacade;
use PoP\GraphQLAPIQuery\Facades\GraphQLQueryConvertorFacade;

class GraphQLPersistedQueryUtils
{
    public static function addPersistedQuery(string $queryName, string $graphQLQuery, ?string $description = null): void
    {
        $queryCatalogueManager = PersistedQueryManagerFacade::getInstance();
        $graphQLQueryConvertor = GraphQLQueryConvertorFacade::getInstance();
        $fieldQuery = $graphQLQueryConvertor->convertFromGraphQLToFieldQuery($graphQLQuery);
        $queryCatalogueManager->add($queryName, $fieldQuery, $description);
    }

    public static function addPersistedFragment(string $fragmentName, string $graphQLFragment, ?string $description = null): void
    {
        $fragmentCatalogueManager = PersistedFragmentManagerFacade::getInstance();
        $graphQLQueryConvertor = GraphQLQueryConvertorFacade::getInstance();
        $fieldQuery = $graphQLQueryConvertor->convertFromGraphQLToFieldQuery($graphQLFragment);
        $fragmentCatalogueManager->add($fragmentName, $fieldQuery, $description);
    }
}
