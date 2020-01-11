<?php
namespace PoP\GraphQL;

use PoP\Root\Component\AbstractComponent;
use PoP\Root\Component\CanDisableComponentTrait;
use PoP\GraphQLAPIRequest\Component as GraphQLAPIRequestComponent;

/**
 * Initialize component
 */
class Component extends AbstractComponent
{
    // const VERSION = '0.1.0';
    use CanDisableComponentTrait;

    /**
     * Initialize services
     */
    public static function init()
    {
        if (self::isEnabled()) {
            parent::init();
        }
    }

    protected static function resolveEnabled()
    {
        return GraphQLAPIRequestComponent::isEnabled();
    }
}
