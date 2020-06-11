<?php

declare(strict_types=1);

namespace PoP\GraphQL\DataStructureFormatters;

use PoP\ComponentModel\State\ApplicationState;

class GraphQLDataStructureFormatter extends \PoP\GraphQLAPI\DataStructureFormatters\GraphQLDataStructureFormatter
{
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
     * Do not print "type" => "query"
     *
     * @return array
     */
    protected function getQueryEntryExtensions(): array
    {
        $vars = ApplicationState::getVars();
        if ($vars['standard-graphql']) {
            return [];
        }
        return parent::getQueryEntryExtensions();
    }
}
