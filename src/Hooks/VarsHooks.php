<?php
namespace PoP\GraphQL\Hooks;

use PoP\GraphQL\Configuration\Request;
use PoP\Engine\Hooks\AbstractHookSet;
use PoP\ComponentModel\State\ApplicationState;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\ModelInstance\ModelInstance;
use PoP\GraphQLAPI\DataStructureFormatters\GraphQLDataStructureFormatter;

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

    public function addVars($vars_in_array)
    {
        $vars = &$vars_in_array[0];
        if ($vars['scheme'] == \POP_SCHEME_API && $vars['datastructure'] == GraphQLDataStructureFormatter::getName()) {
            $vars['edit-schema'] = Request::editSchema();
        }
    }

    public function getModelInstanceComponentsFromVars($components)
    {
        $vars = ApplicationState::getVars();
        if (isset($vars['edit-schema'])) {
            $components[] = TranslationAPIFacade::getInstance()->__('edit schema:', 'graphql') . $vars['edit-schema'];
        }

        return $components;
    }
}
