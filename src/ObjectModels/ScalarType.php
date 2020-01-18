<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\AbstractType;
use PoP\GraphQL\ObjectModels\NonDocumentableTypeTrait;

class ScalarType extends AbstractType
{
    use NonDocumentableTypeTrait;

    protected $name;
    public function __construct(array &$fullSchemaDefinition, array $schemaDefinitionPath, string $name)
    {
        parent::__construct($fullSchemaDefinition, $schemaDefinitionPath);
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getKind(): string
    {
        return TypeKinds::SCALAR;
    }
}
