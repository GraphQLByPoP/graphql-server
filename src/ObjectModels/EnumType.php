<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\AbstractType;

class EnumType extends AbstractType
{
    protected $enumValues;
    function __construct(array $enumValues)
    {
        $this->enumValues = $enumValues;
    }
    public function getID()
    {
        return TypeUtils::getEnumTypeID($this->getKind(), $this->enumValues);
    }
    public function getKind(): string
    {
        return TypeKinds::ENUM;
    }
    public function getEnumValues(bool $includeDeprecated = false): array
    {
        return $this->enumValues;
    }
    public function getEnumValueIDs(bool $includeDeprecated = false): array
    {
        return array_map(
            function($enumValueDefinition) {
                return TypeUtils::getEnumTypeID(TypeKinds::ENUM, $enumValueDefinition);
            },
            $this->getEnumValues($includeDeprecated)
        );
    }
}
