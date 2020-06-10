<?php

declare(strict_types=1);

namespace PoP\GraphQL;

use PoP\Root\Component\AbstractComponent;
use PoP\Root\Component\YAMLServicesTrait;
use PoP\GraphQL\Config\ServiceConfiguration;
use PoP\Root\Component\CanDisableComponentTrait;
use PoP\ComponentModel\Container\ContainerBuilderUtils;
use PoP\GraphQLAPIRequest\Component as GraphQLAPIRequestComponent;
use PoP\GraphQLAPIQuery\ComponentConfiguration as GraphQLAPIQueryComponentConfiguration;

/**
 * Initialize component
 */
class Component extends AbstractComponent
{
    use YAMLServicesTrait, CanDisableComponentTrait;

    // const VERSION = '0.1.0';

    public static function getDependedComponentClasses(): array
    {
        return [
            \PoP\GraphQLAPIRequest\Component::class,
        ];
    }

    /**
     * All conditional component classes that this component depends upon, to initialize them
     *
     * @return array
     */
    public static function getDependedConditionalComponentClasses(): array
    {
        return [
            \PoP\AccessControl\Component::class,
        ];
    }

    /**
     * Initialize services
     */
    protected static function doInitialize(
        array $configuration = [],
        bool $skipSchema = false,
        array $skipSchemaComponentClasses = []
    ): void {
        if (self::isEnabled()) {
            parent::doInitialize($configuration, $skipSchema, $skipSchemaComponentClasses);
            ComponentConfiguration::setConfiguration($configuration);
            self::initYAMLServices(dirname(__DIR__));
            self::maybeInitYAMLSchemaServices(dirname(__DIR__), $skipSchema);
            ServiceConfiguration::initialize();
        }
    }

    protected static function resolveEnabled()
    {
        return GraphQLAPIRequestComponent::isEnabled();
    }

    /**
     * Boot component
     *
     * @return void
     */
    public static function beforeBoot(): void
    {
        parent::beforeBoot();

        // Initialize classes
        ContainerBuilderUtils::registerTypeResolversFromNamespace(__NAMESPACE__ . '\\TypeResolvers');
        ContainerBuilderUtils::instantiateNamespaceServices(__NAMESPACE__ . '\\Hooks');
        ContainerBuilderUtils::attachFieldResolversFromNamespace(__NAMESPACE__ . '\\FieldResolvers', false);
        ContainerBuilderUtils::attachAndRegisterDirectiveResolversFromNamespace(__NAMESPACE__ . '\\DirectiveResolvers', false);
        // Attach the Extensions with a higher priority, so it executes first
        ContainerBuilderUtils::attachFieldResolversFromNamespace(__NAMESPACE__ . '\\FieldResolvers\\Extensions', false, 100);

        // Boot conditional on API package being installed
        if (class_exists('\PoP\AccessControl\Component')) {
            \PoP\GraphQL\Conditional\AccessControl\ComponentBoot::beforeBoot();
        }

        // Boot conditional on having variables treated as expressions for @export directive
        if (GraphQLAPIQueryComponentConfiguration::enableVariablesAsExpressions()) {
            ContainerBuilderUtils::attachFieldResolversFromNamespace(__NAMESPACE__ . '\\FieldResolvers\\ConditionalOnEnvironment\\VariablesAsExpressions');
        }
    }
}
