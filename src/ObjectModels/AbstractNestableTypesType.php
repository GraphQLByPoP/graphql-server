<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\AbstractType;

abstract class AbstractNestableTypesType extends AbstractType
{
    protected $nestedType;
    public function __construct(array &$fullSchemaDefinition, array $schemaDefinitionPath, AbstractType $nestedType)
    {
        parent::__construct($fullSchemaDefinition, $schemaDefinitionPath);
        $this->nestedType = $nestedType;
    }
}
