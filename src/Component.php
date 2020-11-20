<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer;

use PoP\Root\Component\AbstractComponent;
use PoP\Root\Component\YAMLServicesTrait;
use GraphQLByPoP\GraphQLServer\Config\ServiceConfiguration;
use PoP\Root\Component\CanDisableComponentTrait;
use PoP\ComponentModel\Container\ContainerBuilderUtils;
use GraphQLByPoP\GraphQLRequest\Component as GraphQLRequestComponent;
use GraphQLByPoP\GraphQLQuery\ComponentConfiguration as GraphQLQueryComponentConfiguration;
use GraphQLByPoP\GraphQLServer\ComponentConfiguration;
use GraphQLByPoP\GraphQLRequest\ComponentConfiguration as GraphQLRequestComponentConfiguration;
use GraphQLByPoP\GraphQLServer\DirectiveResolvers\ConditionalOnEnvironment\ExportDirectiveResolver;
use GraphQLByPoP\GraphQLServer\DirectiveResolvers\ConditionalOnEnvironment\RemoveIfNullDirectiveResolver;
use PoP\ComponentModel\AttachableExtensions\AttachableExtensionGroups;
use PoP\API\ComponentConfiguration as APIComponentConfiguration;
use GraphQLByPoP\GraphQLServer\Configuration\Request;
use GraphQLByPoP\GraphQLServer\Environment;
use PoP\Engine\Environment as EngineEnvironment;
use GraphQLByPoP\GraphQLServer\Component as GraphQLServerComponent;
use PoP\Engine\Component as EngineComponent;

/**
 * Initialize component
 */
class Component extends AbstractComponent
{
    use YAMLServicesTrait, CanDisableComponentTrait;

    // const VERSION = '0.1.0';

    /**
     * Classes from PoP components that must be initialized before this component
     *
     * @return string[]
     */
    public static function getDependedComponentClasses(): array
    {
        return [
            \GraphQLByPoP\GraphQLRequest\Component::class,
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
     * Set the default component configuration
     *
     * @param array<string, mixed> $componentClassConfiguration
     */
    public static function customizeComponentClassConfiguration(
        array &$componentClassConfiguration
    ): void {
        // The mutation scheme can be set by param ?mutation_scheme=..., with values:
        // - "standard" => Use QueryRoot and MutationRoot
        // - "nested" => Use Root, and nested mutations with redundant root fields
        // - "lean_nested" => Use Root, and nested mutations without redundant root fields
        if (Environment::enableSettingMutationSchemeByURLParam()) {
            if ($mutationScheme = Request::getMutationScheme()) {
                $componentClassConfiguration[GraphQLServerComponent::class][Environment::ENABLE_NESTED_MUTATIONS] = $mutationScheme != Request::URLPARAM_VALUE_MUTATION_SCHEME_STANDARD;
                $componentClassConfiguration[EngineComponent::class][EngineEnvironment::DISABLE_REDUNDANT_ROOT_TYPE_MUTATION_FIELDS] = $mutationScheme == Request::URLPARAM_VALUE_MUTATION_SCHEME_NESTED_WITHOUT_REDUNDANT_ROOT_FIELDS;
            }
        }
    }

    /**
     * Initialize services
     *
     * @param array<string, mixed> $configuration
     * @param string[] $skipSchemaComponentClasses
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
        return GraphQLRequestComponent::isEnabled();
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
        // ContainerBuilderUtils::attachAndRegisterDirectiveResolversFromNamespace(__NAMESPACE__ . '\\DirectiveResolvers', false);
        // Attach the Extensions with a higher priority, so it executes first
        ContainerBuilderUtils::attachFieldResolversFromNamespace(__NAMESPACE__ . '\\FieldResolvers\\Extensions', false, 100);

        // Conditional on Environment
        // The @export directive depends on the Multiple Query Execution being enabled
        if (GraphQLRequestComponentConfiguration::enableMultipleQueryExecution()) {
            ExportDirectiveResolver::attach(AttachableExtensionGroups::DIRECTIVERESOLVERS);
        }
        // Attach @removeIfNull?
        if (ComponentConfiguration::enableRemoveIfNullDirective()) {
            RemoveIfNullDirectiveResolver::attach(AttachableExtensionGroups::DIRECTIVERESOLVERS);
        }

        // Boot conditional on API package being installed
        if (class_exists('\PoP\AccessControl\Component')) {
            \GraphQLByPoP\GraphQLServer\Conditional\AccessControl\ComponentBoot::beforeBoot();
        }

        // Boot conditional on having variables treated as expressions for @export directive
        if (GraphQLQueryComponentConfiguration::enableVariablesAsExpressions()) {
            ContainerBuilderUtils::attachFieldResolversFromNamespace(__NAMESPACE__ . '\\FieldResolvers\\ConditionalOnEnvironment\\VariablesAsExpressions');
        }
        // Boot conditional on having embeddable fields
        if (APIComponentConfiguration::enableEmbeddableFields()) {
            ContainerBuilderUtils::attachFieldResolversFromNamespace(__NAMESPACE__ . '\\FieldResolvers\\ConditionalOnEnvironment\\EmbeddableFields');
        }
        // Boot conditional on having nested mutations disabled
        if (!ComponentConfiguration::enableNestedMutations()) {
            ContainerBuilderUtils::attachFieldResolversFromNamespace(__NAMESPACE__ . '\\FieldResolvers\\ConditionalOnEnvironment\\DisabledNestedMutations');
        }
    }
}
