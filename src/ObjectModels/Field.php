<?php

declare(strict_types=1);

namespace PoP\GraphQLServer\ObjectModels;

use PoP\API\Schema\SchemaDefinition;
use PoP\GraphQLServer\ObjectModels\HasArgsSchemaDefinitionReferenceTrait;
use PoP\GraphQLServer\ObjectModels\HasTypeSchemaDefinitionReferenceTrait;

class Field extends AbstractSchemaDefinitionReferenceObject
{
    use HasTypeSchemaDefinitionReferenceTrait, HasArgsSchemaDefinitionReferenceTrait;

    public function __construct(array &$fullSchemaDefinition, array $schemaDefinitionPath, array $customDefinition = [])
    {
        parent::__construct($fullSchemaDefinition, $schemaDefinitionPath, $customDefinition);

        $this->initArgs($fullSchemaDefinition, $schemaDefinitionPath);
    }
    public function initializeTypeDependencies(): void
    {
        $this->initType();
        $this->initializeArgsTypeDependencies();
    }
    public function getName(): string
    {
        return $this->schemaDefinition[SchemaDefinition::ARGNAME_NAME];
    }
    public function getDescription(): ?string
    {
        return $this->schemaDefinition[SchemaDefinition::ARGNAME_DESCRIPTION];
    }
    public function isDeprecated(): bool
    {
        return $this->schemaDefinition[SchemaDefinition::ARGNAME_DEPRECATED] ?? false;
    }
    public function getDeprecationDescription(): ?string
    {
        return $this->schemaDefinition[SchemaDefinition::ARGNAME_DEPRECATIONDESCRIPTION];
    }
    public function getExtensions(): array
    {
        $extensions = [];
        if ($version = $this->schemaDefinition[SchemaDefinition::ARGNAME_VERSION]) {
            $extensions[SchemaDefinition::ARGNAME_VERSION] = $version;
        }
        return $extensions;
    }
}
