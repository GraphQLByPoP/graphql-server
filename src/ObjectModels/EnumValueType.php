<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\GraphQL\Facades\Registries\FieldRegistryFacade;

class EnumValueType {

    protected $fieldID;
    protected $value;
    protected $enumValueDefinition;
    public function __construct(string $fieldID, $value)
    {
        $this->fieldID = $fieldID;
        $this->value = $value;
    }
    public function getID()
    {
        return TypeUtils::getEnumValueID($this->fieldID, $this->value);
    }
    public function getValue()
    {
        return $this->value;
    }

    protected function maybeInitEnumValueDefinition(): void
    {
        if (is_null($this->enumValueDefinition)) {
            // Extract all the properties from the fieldRegistry
            $fieldRegistry = FieldRegistryFacade::getInstance();
            $fieldDefinition = $fieldRegistry->getFieldDefinition($this->fieldID);
            $this->enumValueDefinition = $fieldDefinition[SchemaDefinition::ARGNAME_ENUMVALUES][$this->getName()];
        }
    }
    public function getName(): string
    {
        return (string)$this->getValue();
    }
    public function getDescription(): ?string
    {
        $this->maybeInitEnumValueDefinition();
        return $this->enumValueDefinition[SchemaDefinition::ARGNAME_DESCRIPTION];
    }
    public function isDeprecated(): bool
    {
        $this->maybeInitEnumValueDefinition();
        return $this->enumValueDefinition[SchemaDefinition::ARGNAME_DEPRECATED] ?? false;
    }
    public function getDeprecatedReason(): ?string
    {
        $this->maybeInitEnumValueDefinition();
        return $this->enumValueDefinition[SchemaDefinition::ARGNAME_DEPRECATIONDESCRIPTION];
    }
}
