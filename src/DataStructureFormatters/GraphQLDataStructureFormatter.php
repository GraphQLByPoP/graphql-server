<?php

declare(strict_types=1);

namespace PoP\GraphQL\DataStructureFormatters;

use PoP\ComponentModel\State\ApplicationState;

class GraphQLDataStructureFormatter extends \PoP\GraphQLAPI\DataStructureFormatters\GraphQLDataStructureFormatter
{
    /**
     * Do not add the extensions naturally available to PoP for standard GraphQL
     *
     * @return boolean
     */
    protected function addNativeExtensions(): bool
    {
        $vars = ApplicationState::getVars();
        if ($vars['standard-graphql']) {
            return false;
        }
        return parent::addNativeExtensions();
    }

    /**
     * Override the parent function, to place the locations from outside extensions
     *
     * @param array $entry
     * @param array $extensions
     * @return void
     */
    protected function addExtensions(array &$entry, array $extensions): void
    {
        parent::addExtensions($entry, $extensions);
        if ($location = $entry['extensions']['location']) {
            unset($entry['extensions']['location']);
            if (!$entry['extensions']) {
                unset($entry['extensions']);
            }
            $entry['location'] = $location;
        }
    }
}
