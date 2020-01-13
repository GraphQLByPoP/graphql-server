<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\AbstractResolvableType;
use PoP\GraphQL\Facades\Registries\TypeRegistryFacade;
use PoP\GraphQL\ObjectModels\HasPossibleTypesTypeTrait;
use PoP\GraphQL\ObjectModels\HasPossibleTypesTypeInterface;

class UnionType extends AbstractResolvableType implements HasPossibleTypesTypeInterface
{
    use HasPossibleTypesTypeTrait;

    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->initPossibleTypes($name);
    }

    public function getKind(): string
    {
        return TypeKinds::UNION;
    }

    public function getTypeDefinition(string $name): array
    {
        $typeRegistry = TypeRegistryFacade::getInstance();
        return $typeRegistry->getTypeDefinition($name);
    }
}
