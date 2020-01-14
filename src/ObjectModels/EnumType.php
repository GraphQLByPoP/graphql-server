<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\AbstractType;
use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\GraphQL\Facades\Registries\FieldRegistryFacade;

class EnumType extends AbstractType
{
    protected $fieldID;
    // protected $name;
    protected $enumValues;
    function __construct(string $fieldID/*, string $name*/)
    {
        $this->fieldID = $fieldID;
        // $this->name = $name;
    }
    public function getID()
    {
        return TypeUtils::getEnumTypeID($this->getKind(), $this->fieldID/*, $this->name*/);
    }
    public function getKind(): string
    {
        return TypeKinds::ENUM;
    }
    public function getEnumValues(bool $includeDeprecated = false): array
    {
        if (is_null($this->enumValues)) {
            // Extract all the properties from the fieldRegistry
            $fieldRegistry = FieldRegistryFacade::getInstance();
            $fieldDefinition = $fieldRegistry->getFieldDefinition($this->fieldID);
            $this->enumValues = $fieldDefinition[SchemaDefinition::ARGNAME_ENUMVALUES];
        }
        return $this->enumValues;
    }
    public function getEnumValueIDs(bool $includeDeprecated = false): array
    {
        return array_keys($this->getEnumValues($includeDeprecated));
    }
}
