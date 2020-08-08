<?php

declare(strict_types=1);

namespace PoP\GraphQLServer\ObjectModels;

use PoP\API\Schema\SchemaDefinition;
use PoP\GraphQLServer\Schema\SchemaDefinition as GraphQLServerSchemaDefinition;
use PoP\GraphQLServer\Syntax\SyntaxHelpers;
use PoP\GraphQLServer\ObjectModels\AbstractType;
use PoP\GraphQLServer\ObjectModels\InputObjectType;
use PoP\GraphQLServer\Schema\SchemaDefinitionHelpers;
use PoP\GraphQLServer\Facades\Registries\SchemaDefinitionReferenceRegistryFacade;

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
                    SyntaxHelpers::getNonNullTypeNestedTypeName($typeName)
                )
            );
        }

        // Check if it is an array
        if (SyntaxHelpers::isListType($typeName)) {
            return new ListType(
                $this->fullSchemaDefinition,
                $this->schemaDefinitionPath,
                $this->getTypeFromTypeName(
                    SyntaxHelpers::getListTypeNestedTypeName($typeName)
                )
            );
        }

        // Check if it is an enum type
        if ($typeName == GraphQLServerSchemaDefinition::TYPE_ENUM) {
            return new EnumType(
                $this->fullSchemaDefinition,
                $this->schemaDefinitionPath
            );
        }

        // Check if it is an inputObject type
        if ($typeName == GraphQLServerSchemaDefinition::TYPE_INPUT_OBJECT) {
            return new InputObjectType(
                $this->fullSchemaDefinition,
                $this->schemaDefinitionPath
            );
        }

        // By now, it's either an InterfaceType, UnionType, ObjectType or a ScalarType. Since they have all been registered, we can get their references from the registry
        $typeSchemaDefinitionPath = [
            SchemaDefinition::ARGNAME_TYPES,
            $typeName,
        ];
        $schemaDefinitionID = SchemaDefinitionHelpers::getID($typeSchemaDefinitionPath);
        $schemaDefinitionReferenceRegistry = SchemaDefinitionReferenceRegistryFacade::getInstance();
        return $schemaDefinitionReferenceRegistry->getSchemaDefinitionReference($schemaDefinitionID);
    }
}
