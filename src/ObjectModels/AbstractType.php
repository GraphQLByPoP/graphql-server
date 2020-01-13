<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\API\Schema\SchemaDefinition;

abstract class AbstractType
{
    protected $name;
    public function __construct(string $name)
    {
        $this->name = $name;

        // Extract properties (such as description) from the typeRegistry
        $this->typeDefinition = $this->getTypeDefinition($name);
    }
    public function getID()
    {
        return TypeUtils::getID($this->getKind(), $this->name);
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getDescription(): ?string
    {
        return $this->typeDefinition[SchemaDefinition::ARGNAME_DESCRIPTION];
    }
    abstract public function getKind();
    abstract public function getTypeDefinition(string $name): array;
}
