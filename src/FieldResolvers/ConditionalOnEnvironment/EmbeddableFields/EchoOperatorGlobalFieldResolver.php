<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer\FieldResolvers\ConditionalOnEnvironment\EmbeddableFields;

use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\Engine\FieldResolvers\OperatorGlobalFieldResolver;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;

/**
 * When Embeddable Fields is enabled, register the `echo` field
 */
class EchoOperatorGlobalFieldResolver extends OperatorGlobalFieldResolver
{
    /**
     * By making it not global, it gets registered on each single type.
     * Otherwise, it is not exposed in the schema
     */
    public function isGlobal(TypeResolverInterface $typeResolver, string $fieldName): bool
    {
        return false;
    }

    /**
     * Higher priority => Process before the global fieldResolver,
     * so this one gets registered (otherwise, since `ADD_GLOBAL_FIELDS_TO_SCHEMA`
     * is false, the field would be removed)
     */
    public static function getPriorityToAttachClasses(): ?int
    {
        return 20;
    }

    /**
     * Only the `echo` field is to be exposed
     *
     * @return string[]
     */
    public static function getFieldNamesToResolve(): array
    {
        return [
            'echo',
        ];
    }

    /**
     * Change the type from mixed to string
     */
    public function getSchemaFieldType(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $types = [
            'echo' => SchemaDefinition::TYPE_STRING,
        ];
        return $types[$fieldName] ?? parent::getSchemaFieldType($typeResolver, $fieldName);
    }

    /**
     * Change the type from mixed to string
     */
    public function getSchemaFieldArgs(TypeResolverInterface $typeResolver, string $fieldName): array
    {
        $schemaFieldArgs = parent::getSchemaFieldArgs($typeResolver, $fieldName);
        $translationAPI = TranslationAPIFacade::getInstance();
        switch ($fieldName) {
            case 'echo':
                return array_merge(
                    $schemaFieldArgs,
                    [
                        [
                            SchemaDefinition::ARGNAME_NAME => 'value',
                            SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_STRING,
                            SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The input string to be echoed back', 'graphql-api'),
                            SchemaDefinition::ARGNAME_MANDATORY => true,
                        ],
                    ]
                );
        }

        return $schemaFieldArgs;
    }

    /**
     * Change the type from mixed to string
     */
    public function getSchemaFieldDescription(TypeResolverInterface $typeResolver, string $fieldName): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        $descriptions = [
            'echo' => $translationAPI->__('Repeat back the input string', 'graphql-api'),
        ];
        return $descriptions[$fieldName] ?? parent::getSchemaFieldDescription($typeResolver, $fieldName);
    }
}
