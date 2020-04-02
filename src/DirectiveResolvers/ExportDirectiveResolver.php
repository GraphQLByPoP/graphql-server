<?php
namespace PoP\GraphQL\DirectiveResolvers;

use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\ComponentModel\Facades\Schema\FieldQueryInterpreterFacade;
use PoP\ComponentModel\DirectiveResolvers\AbstractGlobalDirectiveResolver;
use PoP\ComponentModel\GeneralUtils;

/**
 * @export directive, to make the value of a field available through a variable (appending "$"):
 *
 * ```graphql
 * query GetMyPosts($me:ID) {
 *   me {
 *     id @export(as:"me")
 *   }
 *   posts(author:$me) {
 *     id
 *     title
 *   }
 * }
 * ```
 */
class ExportDirectiveResolver extends AbstractGlobalDirectiveResolver
{
    const DIRECTIVE_NAME = 'export';
    public static function getDirectiveName(): string
    {
        return self::DIRECTIVE_NAME;
    }

    /**
     * Export the value of a field, assigning it to a variable.
     *
     * It works with a single value. This query:
     *
     * ```graphql
     * query {
     *   me {
     *     id @export(as:"myID")
     *   }
     * }
     * ```
     *
     * ...exports variable $myID with the user ID as value.
     *
     * If several values are exported, then the variable will contain an array with all of them. This query:
     *
     * ```graphql
     * query {
     *   posts {
     *     id @export(as:"postIDs")
     *   }
     * }
     * ```
     *
     * ... exports variable $postIDs as an array with the IDs of all posts
     *
     * If several fields are exported with the same variable name on the same object, then the variable is assigned a dictionary of field/value. This query:
     *
     * ```graphql
     * query {
     *   me {
     *     id @export(as:"myData")
     *     name @export(as:"myData")
     *   }
     * }
     * ```
     *
     * ... exports variable $myData as a dictionary {"id": user ID, "name": user name}. This is the same as executing:
     *
     * ```graphql
     * query {
     *   me @export(as:"myData") {
     *     id
     *     name
     *   }
     * }
     * ```
     *
     * If over an array of objects, several fields are exported with the same variable name, then the variable is assigned an array containing dictionaries of field/value. This query:
     *
     * ```graphql
     * query {
     *   posts {
     *     id @export(as:"postIDsAndTitles")
     *     title @export(as:"postIDsAndTitles")
     *   }
     * }
     * ```
     *
     * ... exports variable $postIDsAndTitles as an array, where each item is a dictionary {"id": post ID, "title": post title}. This is the same as executing:
     *
     * ```graphql
     * query {
     *   posts @export(as:"postIDsAndTitles") {
     *     id
     *     title
     *   }
     * }
     * ```
     *
     * @param TypeResolverInterface $typeResolver
     * @param array $idsDataFields
     * @param array $succeedingPipelineIDsDataFields
     * @param array $succeedingPipelineDirectiveResolverInstances
     * @param array $resultIDItems
     * @param array $unionDBKeyIDs
     * @param array $dbItems
     * @param array $previousDBItems
     * @param array $variables
     * @param array $messages
     * @param array $dbErrors
     * @param array $dbWarnings
     * @param array $dbDeprecations
     * @param array $schemaErrors
     * @param array $schemaWarnings
     * @param array $schemaDeprecations
     * @return void
     */
    public function resolveDirective(TypeResolverInterface $typeResolver, array &$idsDataFields, array &$succeedingPipelineIDsDataFields, array &$succeedingPipelineDirectiveResolverInstances, array &$resultIDItems, array &$unionDBKeyIDs, array &$dbItems, array &$previousDBItems, array &$variables, array &$messages, array &$dbErrors, array &$dbWarnings, array &$dbDeprecations, array &$schemaErrors, array &$schemaWarnings, array &$schemaDeprecations)
    {
        $fieldQueryInterpreter = FieldQueryInterpreterFacade::getInstance();
        $ids = array_keys($idsDataFields);

        /**
         * Single object. 2 cases:
         *
         * 1. Single value: when there is a single field
         * 2. Dictionary: otherwise
         */
        if (count($ids) == 1) {
            $id = $ids[0];
            $fields = $idsDataFields[(string)$id]['direct'];
            /**
             * Case 1: Single field => single value:
             *
             * ```graphql
             * query {
             *   me {
             *     id @export(as:"myID")
             *   }
             * }
             * ```
             */
            if (count($fields) == 1) {
                $field = $fields[0];
                $fieldOutputKey = $fieldQueryInterpreter->getFieldOutputKey($field);
                $value = $dbItems[(string)$id][$fieldOutputKey];
                $this->setVariable($variables, $value);
                return;
            }

            /**
             * Case 2: Multiple fields => dictionary:
             *
             * ```graphql
             * query {
             *   me {
             *     id @export(as:"myData")
             *     name @export(as:"myData")
             *   }
             * }
             * ```
             *
             * or:
             *
             * ```graphql
             * query {
             *   me @export(as:"myData") {
             *     id
             *     name
             *   }
             * }
             * ```
             */
            $value = [];
            foreach ($fields as $field) {
                $fieldOutputKey = $fieldQueryInterpreter->getFieldOutputKey($field);
                $value[$fieldOutputKey] = $dbItems[(string)$id][$fieldOutputKey];
            }
            $this->setVariable($variables, $value);
            return;
        }

        /**
         * Multiple objects. 2 cases:
         *
         * 1. Array of values: When all objects have a single field, and this field is the same for all objects
         * 2. Array of dictionaries: Otherwise
         */
        $value = [];
        $allFields = array_unique(GeneralUtils::arrayFlatten(array_map(
            function($idDataFields) {
                return $idDataFields['direct'];
            },
            $idsDataFields
        )));

        /**
         * Case 1: Array of values
         *
         * ```graphql
         * query {
         *   posts {
         *     id @export(as:"postIDs")
         *   }
         * }
         * ```
         */
        if (count($allFields) == 1) {
            $field = $allFields[0];
            $fieldOutputKey = $fieldQueryInterpreter->getFieldOutputKey($field);
            foreach ($ids as $id) {
                $value[] = $dbItems[(string)$id][$fieldOutputKey];
            }
            $this->setVariable($variables, $value);
            return;
        }

        /**
         * Case 2: Array of dictionaries:
         *
         * ```graphql
         * query {
         *   posts {
         *     id @export(as:"postIDsAndTitles")
         *     title @export(as:"postIDsAndTitles")
         *   }
         * }
         * ```
         *
         * or:
         *
         * ```graphql
         * query {
         *   posts @export(as:"postIDsAndTitles") {
         *     id
         *     title
         *   }
         * }
         * ```
         */
        foreach ($idsDataFields as $id => $dataFields) {
            $dictionary = [];
            foreach ($dataFields['direct'] as $field) {
                $fieldOutputKey = $fieldQueryInterpreter->getFieldOutputKey($field);
                $dictionary[] = $dbItems[(string)$id][$fieldOutputKey];
            }
            $value[] = $dictionary;
        }
        $this->setVariable($variables, $value);
        return;
    }

    protected function setVariable(array &$variables, $value): void
    {
        $variableName = $this->directiveArgsForSchema['as'];
        $variables[$variableName] = $value;
    }

    public function getSchemaDirectiveDescription(TypeResolverInterface $typeResolver): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        return $translationAPI->__('Exports a field value as a variable', 'graphql');
    }
    public function getSchemaDirectiveArgs(TypeResolverInterface $typeResolver): array
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        return [
            [
                SchemaDefinition::ARGNAME_NAME => 'as',
                SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_STRING,
                SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('Name of the variable', 'graphql'),
                SchemaDefinition::ARGNAME_MANDATORY => true,
            ],
        ];
    }
}
