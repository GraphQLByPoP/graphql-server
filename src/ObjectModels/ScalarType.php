<?php

declare(strict_types=1);

namespace PoP\GraphQLServer\ObjectModels;

use PoP\GraphQLServer\ObjectModels\AbstractType;
use PoP\GraphQLServer\ObjectModels\NonDocumentableTypeTrait;

class ScalarType extends AbstractType
{
    use NonDocumentableTypeTrait;

    protected $name;
    public function __construct(array &$fullSchemaDefinition, array $schemaDefinitionPath, string $name, array $customDefinition = [])
    {
        parent::__construct($fullSchemaDefinition, $schemaDefinitionPath, $customDefinition);
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
