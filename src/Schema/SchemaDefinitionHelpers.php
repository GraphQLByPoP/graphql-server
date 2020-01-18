<?php
namespace PoP\GraphQL\Schema;

use PoP\GraphQL\ObjectModels\Field;
use PoP\GraphQL\Facades\Registries\SchemaDefinitionReferenceRegistryFacade;

// use PoP\GraphQL\ObjectModels\UnionType;
// use PoP\GraphQL\ObjectModels\ObjectType;
// use PoP\ComponentModel\Schema\SchemaDefinition;

class SchemaDefinitionHelpers
{
    public const PATH_SEPARATOR = '.';
    // public const DEPENDENT_TOKEN = '*';

    public static function getID(array $schemaDefinitionPath): string
    {
        // return $this->getObjectModelFamily().($this->schemaDefinitionPath ? implode(TypeUtils::PATH_SEPARATOR, $this->schemaDefinitionPath) : '');
        return implode(
            self::PATH_SEPARATOR,
            $schemaDefinitionPath
        );
    }
    public static function &advancePointerToPath(array &$schemaDefinition, array $schemaDefinitionPath)
    {
        $schemaDefinitionPointer = &$schemaDefinition;
        foreach ($schemaDefinitionPath as $pathLevel) {
            $schemaDefinitionPointer = &$schemaDefinitionPointer[$pathLevel];
        }
        return $schemaDefinitionPointer;
    }
    public static function initFieldsFromPath(array &$fullSchemaDefinition, array $fieldSchemaDefinitionPath): array
    {
        $fieldSchemaDefinitionPointer = self::advancePointerToPath($fullSchemaDefinition, $fieldSchemaDefinitionPath);
        $fields = [];
        foreach (array_keys($fieldSchemaDefinitionPointer) as $fieldName) {
            $fields[] = new Field(
                $fullSchemaDefinition,
                array_merge(
                    $fieldSchemaDefinitionPath,
                    [
                        $fieldName
                    ]
                )
            );
        }
        return $fields;
    }
    public static function retrieveFieldsFromPath(array &$fullSchemaDefinition, array $fieldSchemaDefinitionPath): array
    {
        $fieldSchemaDefinitionPointer = self::advancePointerToPath($fullSchemaDefinition, $fieldSchemaDefinitionPath);
        $schemaDefinitionReferenceRegistry = SchemaDefinitionReferenceRegistryFacade::getInstance();
        $fields = [];
        foreach (array_keys($fieldSchemaDefinitionPointer) as $fieldName) {
            $schemaDefinitionID = SchemaDefinitionHelpers::getID(array_merge(
                $fieldSchemaDefinitionPath,
                [
                    $fieldName
                ]
            ));
            $fields[] = $schemaDefinitionReferenceRegistry->getSchemaDefinitionReference($schemaDefinitionID);
        }
        return $fields;
    }
    // public static function isResolvableType(string $typeName): bool
    // {
    //     $schemaDefinitionReferenceRegistry = SchemaDefinitionReferenceRegistryFacade::getInstance();
    //     $fullSchemaDefinition = $schemaDefinitionReferenceRegistry->getFullSchemaDefinition();
    //     return in_array(
    //         $typeName,
    //         array_keys($fullSchemaDefinition[SchemaDefinition::ARGNAME_TYPES])
    //     );
    // }
    // /**
    //  * Add a silent level to the path. It is needed to instantiate NonNull/List/Enum/Scalar types, and have their IDs still be unique
    //  * Eg: a NonNullType wraps another AbstractType inside, so by initializing this one with an extra level "dependent", they can both have a unique ID and get their data from the same $schemaDefinition
    //  *
    //  * @param array $schemaDefinitionPath
    //  * @return void
    //  */
    // public static function addDependentLevelToSchemaDefinitionPath(array $schemaDefinitionPath): array
    // {
    //     return $schemaDefinitionPath;
    //     return array_merge(
    //         $schemaDefinitionPath,
    //         [
    //             self::DEPENDENT_TOKEN,
    //         ]
    //     );
    // }

    // public function getType(array &$schemaDefinition, array $typeSchemaDefinitionPath)
    // {
    //     $typeSchemaDefinitionPointer = &$schemaDefinition;
    //     foreach ($typeSchemaDefinitionPath as $pathLevel) {
    //         $typeSchemaDefinitionPointer = &$typeSchemaDefinitionPointer[$pathLevel];
    //     }
    //     $typeSchemaDefinition = $typeSchemaDefinitionPointer;
    //     // The type here can either be an ObjectType or a UnionType
    //     return $typeSchemaDefinition[SchemaDefinition::ARGNAME_IS_UNION] ?
    //         new UnionType($schemaDefinition, $typeSchemaDefinitionPath) :
    //         new ObjectType($schemaDefinition, $typeSchemaDefinitionPath);
    // }
}
