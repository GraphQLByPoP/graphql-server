<?php
namespace PoP\GraphQL\Conditional\AccessControl;

use PoP\AccessControl\Environment;
use PoP\ComponentModel\AttachableExtensions\AttachableExtensionGroups;
use PoP\GraphQL\DirectiveResolvers\ConditionalOnEnvironment\SchemaNoCacheCacheControlDirectiveResolver;

/**
 * Initialize component
 */
class ComponentBoot
{
    /**
     * Boot component
     *
     * @return void
     */
    public static function prematureBoot()
    {
        // Initialize classes
        self::attachDynamicDirectiveResolvers();
    }

    /**
     * Attach directive resolvers based on environment variables
     *
     * @return void
     */
    protected static function attachDynamicDirectiveResolvers()
    {
        /**
         * If `USE_PRIVATE_SCHEMA_MODE` is true, then the schema (as obtained by querying the "__schema" field) is dynamic: Fields will be available or not depending on the user being logged in or not
         * Then, the CacheControl for field "__schema" must be set to "no-cache"
         */
        if (Environment::usePrivateSchemaMode()) {
            SchemaNoCacheCacheControlDirectiveResolver::attach(AttachableExtensionGroups::DIRECTIVERESOLVERS);
        }
    }
}
