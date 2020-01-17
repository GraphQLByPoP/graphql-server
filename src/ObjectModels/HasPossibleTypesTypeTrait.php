<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\API\Schema\SchemaDefinition;

trait HasPossibleTypesTypeTrait
{
    protected $possibleTypes;
    protected function initPossibleTypes(): void
    {
        $this->possibleTypes = [];
        foreach ($this->schemaDefinition[SchemaDefinition::ARGNAME_POSSIBLE_TYPES] as $typeName) {
            // $possibleTypes[] = new Field(
            //     TypeUtils::composeSchemaDefinitionPath('', [SchemaDefinition::ARGNAME_TYPES, $typeName])
            // );
            $possibleTypes[] = TypeUtils::getTypeFromTypeName($typeName, $this->schemaDefinitionPath);
        }
    }
    public function getPossibleTypes(): array
    {
        if (is_null($this->possibleTypes)) {
            $this->initPossibleTypes();
        }
        return $this->possibleTypes;
    }
    public function getPossibleTypeIDs(): array
    {
        return array_map(
            function(AbstractType $type) {
                return $type->getID();
            },
            $this->getPossibleTypes()
        );
    }
    // /**
    //  * Return the interfaces through their ID representation: Kind + Name
    //  *
    //  * @return array
    //  */
    // public function getPossibleTypeIDs(): array
    // {
    //     $typeRegistry = TypeRegistryFacade::getInstance();
    //     $possibleTypeIDs = [];
    //     foreach ($this->possibleTypes as $typeName) {
    //         $typeDefinition = $typeRegistry->getTypeDefinition($typeName);
    //         $typeKind = $typeDefinition[SchemaDefinition::ARGNAME_IS_UNION] ?
    //             TypeKinds::UNION :
    //             TypeKinds::OBJECT;
    //         $possibleTypeIDs[] = TypeUtils::getResolvableTypeID($typeKind, $typeName);
    //     }
    //     return $possibleTypeIDs;
    // }
}
