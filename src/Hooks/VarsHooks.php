<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer\Hooks;

use GraphQLByPoP\GraphQLServer\ComponentConfiguration;
use GraphQLByPoP\GraphQLServer\Configuration\Request;
use PoP\Hooks\AbstractHookSet;
use PoP\ComponentModel\State\ApplicationState;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\ModelInstance\ModelInstance;
use PoP\GraphQLAPI\DataStructureFormatters\GraphQLDataStructureFormatter;
use PoP\API\Response\Schemes as APISchemes;

class VarsHooks extends AbstractHookSet
{
    protected function init()
    {
        $this->hooksAPI->addAction(
            'ApplicationState:addVars',
            array($this, 'addVars'),
            10,
            1
        );
        $this->hooksAPI->addFilter(
            ModelInstance::HOOK_COMPONENTS_RESULT,
            array($this, 'getModelInstanceComponentsFromVars')
        );
    }

    /**
     * @param array<array> $vars_in_array
     */
    public function addVars(array $vars_in_array): void
    {
        [&$vars] = $vars_in_array;
        if ($vars['scheme'] == APISchemes::API && $vars['datastructure'] == GraphQLDataStructureFormatter::getName()) {
            $vars['edit-schema'] = Request::editSchema();
        }
    }

    public function getModelInstanceComponentsFromVars($components)
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        $vars = ApplicationState::getVars();
        if (isset($vars['edit-schema'])) {
            $components[] = $translationAPI->__('edit schema:', 'graphql-server') . $vars['edit-schema'];
        }
        if ($graphQLOperationType = $vars['graphql-operation-type']) {
            $components[] = $translationAPI->__('GraphQL operation type:', 'graphql-server') . $graphQLOperationType;
        }
        $components[] = $translationAPI->__('enable nested mutations:', 'graphql-server') . ComponentConfiguration::enableNestedMutations();

        return $components;
    }
}
