<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\AbstractType;

abstract class AbstractNestableType extends AbstractType
{
    protected $nestedType;
    public function __construct(array &$fullSchemaDefinition, array $schemaDefinitionPath, AbstractType $nestedType)
    {
        parent::__construct($fullSchemaDefinition, $schemaDefinitionPath);
        $this->nestedType = $nestedType;
    }
    public function getNestedType(): AbstractType
    {
        return $this->nestedType;
    }
    public function getNestedTypeID(): string
    {
        return $this->nestedType->getID();
    }
}
