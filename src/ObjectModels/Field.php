<?php

declare(strict_types=1);

namespace GraphQLByPoP\GraphQLServer\ObjectModels;

use PoP\API\Schema\SchemaDefinition;
use GraphQLByPoP\GraphQLServer\ObjectModels\HasArgsSchemaDefinitionReferenceTrait;
use GraphQLByPoP\GraphQLServer\ObjectModels\HasTypeSchemaDefinitionReferenceTrait;

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
        if (isset($this->schemaDefinition[SchemaDefinition::ARGNAME_FIELD_IS_MUTATION]) && $this->schemaDefinition[SchemaDefinition::ARGNAME_FIELD_IS_MUTATION]) {
            $extensions[SchemaDefinition::ARGNAME_FIELD_IS_MUTATION] = true;
        }
        return $extensions;
    }
}
