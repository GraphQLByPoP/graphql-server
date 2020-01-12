<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\API\Schema\SchemaDefinition;
use PoP\GraphQL\Facades\Registries\TypeRegistryFacade;

// use PoP\ComponentModel\Facades\Instances\InstanceManagerFacade;

abstract class AbstractType
{
    protected $name;
    protected $description;
    public function __construct(string $name)
    {
        $this->name = $name;

        // Extract all the properties from the typeRegistry
        $typeRegistry = TypeRegistryFacade::getInstance();
        $typeDefinition = $typeRegistry->getTypeDefinition($name);
        $this->description = $typeDefinition[SchemaDefinition::ARGNAME_DESCRIPTION];
    }
    public function getID()
    {
        return $this->name;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getDescription(): string
    {
        return $this->description;
    }
    abstract public function getKind();
}
