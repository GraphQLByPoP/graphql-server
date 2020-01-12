<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\Facades\Registries\TypeRegistryFacade;

// use PoP\ComponentModel\Facades\Instances\InstanceManagerFacade;

class Type
{
    protected $kind;
    protected $name;
    protected $description;
    public function __construct(string $name)
    {
        $this->name = $name;

        // Extract all the properties from the typeResolverClass
        $typeRegistry = TypeRegistryFacade::getInstance();
        $typeResolver = $typeRegistry->getTypeResolverInstance($name);
        $this->description = $typeResolver->getSchemaTypeDescription();
    }
    public function getID() {
        return $this->name;
    }
    public function getName() {
        return $this->name;
    }
    public function getDescription() {
        return $this->description;
    }
}
