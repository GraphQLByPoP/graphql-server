<?php

declare(strict_types=1);

namespace PoP\GraphQLServer\ObjectModels;

use PoP\GraphQLServer\ObjectModels\AbstractType;

abstract class AbstractNestableType extends AbstractType
{
    protected $nestedType;
    public function __construct(array &$fullSchemaDefinition, array $schemaDefinitionPath, AbstractType $nestedType, array $customDefinition = [])
    {
        parent::__construct($fullSchemaDefinition, $schemaDefinitionPath, $customDefinition);
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
