<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\API\Schema\SchemaDefinition;
use PoP\GraphQL\Syntax\SyntaxHelpers;
use PoP\GraphQL\ObjectModels\AbstractType;
use PoP\GraphQL\ObjectModels\InputObjectType;
use PoP\GraphQL\SchemaDefinition\SchemaDefinitionHelpers;
use PoP\GraphQL\Facades\Registries\SchemaDefinitionReferenceRegistryFacade;

trait ResolveTypeSchemaDefinitionReferenceTrait
{
    protected function getTypeFromTypeName(string $typeName): AbstractType
    {
        // Check if the type is non-null
        if (SyntaxHelpers::isNonNullType($typeName)) {
            return new NonNullType(
                $this->fullSchemaDefinition,
                $this->schemaDefinitionPath,
                $this->getTypeFromTypeName(
                    SyntaxHelpers::getNonNullTypeNestedTypes($typeName)
                )
            );
        }

        // Check if it is an array
        if (SyntaxHelpers::isListType($typeName)) {
            return new ListType(
                $this->fullSchemaDefinition,
                $this->schemaDefinitionPath,
                $this->getTypeFromTypeName(
                    SyntaxHelpers::getListTypeNestedTypes($typeName)
                )
            );
        }

        // Check if it is an enum type
        if ($typeName == SchemaDefinition::TYPE_ENUM) {
            return new EnumType(
                $this->fullSchemaDefinition,
                $this->schemaDefinitionPath
            );
        }

        // Check if it is an enum type
        if ($typeName == SchemaDefinition::TYPE_INPUT_OBJECT) {
            return new InputObjectType(
                $this->fullSchemaDefinition,
                $this->schemaDefinitionPath
            );
        }

        // Any type that has been defined in the schema
        if (SchemaDefinitionHelpers::isResolvableType($typeName)) {
            // Reference to the already-defined type
            $typeSchemaDefinitionPath = [
                SchemaDefinition::ARGNAME_TYPES,
                $typeName,
            ];
            $schemaDefinitionID = SchemaDefinitionHelpers::getID($typeSchemaDefinitionPath);
            $schemaDefinitionReferenceRegistry = SchemaDefinitionReferenceRegistryFacade::getInstance();
            return $schemaDefinitionReferenceRegistry->getSchemaDefinitionReference($schemaDefinitionID);
        }

        // It's a scalar
        return new ScalarType(
            $this->fullSchemaDefinition,
            $this->schemaDefinitionPath
        );
    }
}
