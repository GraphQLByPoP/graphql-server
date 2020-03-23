<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\EnumValue;
use PoP\GraphQL\ObjectModels\AbstractType;
use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\GraphQL\ObjectModels\NonDocumentableTypeTrait;

class EnumType extends AbstractType
{
    use NonDocumentableTypeTrait;

    protected $enumValues;

    public function __construct(array &$fullSchemaDefinition, array $schemaDefinitionPath, array $customDefinition = [])
    {
        parent::__construct($fullSchemaDefinition, $schemaDefinitionPath, $customDefinition);

        $this->initEnumValues($fullSchemaDefinition, $schemaDefinitionPath);
    }
    protected function initEnumValues(array &$fullSchemaDefinition, array $schemaDefinitionPath): void
    {
        $this->enumValues = [];
        if ($enumValues = $this->schemaDefinition[SchemaDefinition::ARGNAME_ENUMVALUES]) {
            foreach (array_keys($enumValues) as $enumValueName) {
                $enumValueSchemaDefinitionPath = array_merge(
                    $schemaDefinitionPath,
                    [
                        SchemaDefinition::ARGNAME_ENUMVALUES,
                        $enumValueName,
                    ]
                );
                $this->enumValues[] = new EnumValue(
                    $fullSchemaDefinition,
                    $enumValueSchemaDefinitionPath
                );
            }
        }
    }

    public function isDynamicType(): bool
    {
        return true;
    }
    public function getKind(): string
    {
        return TypeKinds::ENUM;
    }
    public function getEnumValues(bool $includeDeprecated = false): array
    {
        return $includeDeprecated ?
            $this->enumValues :
            array_filter(
                $this->enumValues,
                function(EnumValue $enumValue) {
                    return !$enumValue->isDeprecated();
                }
            );

    }
    public function getEnumValueIDs(bool $includeDeprecated = false): array
    {
        return array_map(
            function(EnumValue $enumValue) {
                return $enumValue->getID();
            },
            $this->getEnumValues($includeDeprecated)
        );
    }
}
