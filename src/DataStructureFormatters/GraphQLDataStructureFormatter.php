<?php

declare(strict_types=1);

namespace PoP\GraphQL\DataStructureFormatters;

use PoP\ComponentModel\State\ApplicationState;
use PoP\ComponentModel\Feedback\Tokens;
use PoP\ComponentModel\TypeResolvers\UnionTypeHelpers;

/**
 * Change the properties printed for the standard GraphQL response:
 *
 * - extension "entityDBKey" is renamed as "type"
 * - extension "fields" (or "field" if there's one item) instead of "path",
 *   because there are no composable fields
 * - move "location" up from under "extensions"
 *
 * @author Leonardo Losoviz <leo@getpop.org>
 */
class GraphQLDataStructureFormatter extends \PoP\GraphQLAPI\DataStructureFormatters\GraphQLDataStructureFormatter
{
    /**
     * If it is a Union Type, we must remove the "*" from the name
     *
     * @param string $dbKey
     * @return string
     */
    protected function getTypeName(string $dbKey): string
    {
        // The type name is the same as the $dbKey
        $typeName = $dbKey;
        if (UnionTypeHelpers::isUnionType($typeName)) {
            return UnionTypeHelpers::removePrefixFromUnionTypeName($typeName);
        }
        return $typeName;
    }
    /**
     * Change properties for GraphQL
     *
     * @param string $dbKey
     * @param [type] $id
     * @param array $item
     * @return array
     */
    protected function getDBEntryExtensions(string $dbKey, $id, array $item): array
    {
        $vars = ApplicationState::getVars();
        if ($vars['standard-graphql']) {
            $isSinglePath = count($item[Tokens::PATH]) == 1;
            return [
                'type' => $this->getTypeName($dbKey),
                'id' => $id,
                $isSinglePath ? 'field' : 'fields' => $isSinglePath ? $item[Tokens::PATH][0] : $item[Tokens::PATH],
            ];
        }
        return parent::getDBEntryExtensions($dbKey, $id, $item);
    }

    /**
     * Change properties for GraphQL
     *
     * @param string $dbKey
     * @param array $item
     * @return array
     */
    protected function getSchemaEntryExtensions(string $dbKey, array $item): array
    {
        $vars = ApplicationState::getVars();
        if ($vars['standard-graphql']) {
            $isSinglePath = count($item[Tokens::PATH]) == 1;
            return [
                'type' => $this->getTypeName($dbKey),
                $isSinglePath ? 'field' : 'fields' => $isSinglePath ? $item[Tokens::PATH][0] : $item[Tokens::PATH],
            ];
        }
        return parent::getSchemaEntryExtensions($dbKey, $item);
    }
    /**
     * Override the parent function, to place the locations from outside extensions
     *
     * @param string $message
     * @param array $extensions
     * @return void
     */
    protected function getQueryEntry(string $message, array $extensions): array
    {
        $entry = [
            'message' => $message,
        ];
        // Add the "location" directly, not under "extensions"
        if ($location = $extensions['location']) {
            unset($extensions['location']);
            $entry['location'] = $location;
        }
        if ($extensions = array_merge(
            $this->getQueryEntryExtensions(),
            $extensions
        )) {
            $entry['extensions'] = $extensions;
        };
        return $entry;
    }

    /**
     * Change properties for GraphQL
     *
     * @return array
     */
    protected function getQueryEntryExtensions(): array
    {
        $vars = ApplicationState::getVars();
        if ($vars['standard-graphql']) {
            // Do not print "type" => "query"
            return [];
        }
        return parent::getQueryEntryExtensions();
    }
}
