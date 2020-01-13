<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\API\Schema\SchemaDefinition;

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
}
