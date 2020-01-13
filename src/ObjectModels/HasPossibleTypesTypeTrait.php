<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\API\Schema\SchemaDefinition;
use PoP\GraphQL\Facades\Registries\TypeRegistryFacade;

trait HasPossibleTypesTypeTrait
{
    protected $possibleTypes;

    protected function initPossibleTypes(string $name): void
    {
        $typeDefinition = $this->getTypeDefinition($name);
        $this->possibleTypes = $typeDefinition[SchemaDefinition::ARGNAME_POSSIBLE_TYPES];
    }

    public function getPossibleTypes(): array
    {
        return $this->possibleTypes;
    }

    /**
     * Return the interfaces through their ID representation: Kind + Name
     *
     * @return array
     */
    public function getPossibleTypeIDs(): array
    {
        $typeRegistry = TypeRegistryFacade::getInstance();
        $possibleTypeIDs = [];
        foreach ($this->possibleTypes as $typeName) {
            $typeDefinition = $typeRegistry->getTypeDefinition($typeName);
            $typeKind = $typeDefinition[SchemaDefinition::ARGNAME_IS_UNION] ?
                TypeKinds::UNION :
                TypeKinds::OBJECT;
            $possibleTypeIDs[] = TypeUtils::getResolvableTypeID($typeKind, $typeName);
        }
        return $possibleTypeIDs;
    }
}
