<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer\QueryResolution;

class NestedMutationsPrepareGraphQLForExecutionQueryASTTransformationServiceTest extends AbstractPrepareGraphQLForExecutionQueryASTTransformationServiceTest
{
    protected static function isMultipleQueryExecutionEnabled(): bool
    {
        return false;
    }

    protected static function isNestedMutationsEnabled(): bool
    {
        return true;
    }
}
