<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\Field;
use PoP\GraphQL\ObjectModels\AbstractType;
use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\GraphQL\Facades\Registries\FieldRegistryFacade;

class InputValue
{
    protected $field;
    protected $name;
    protected $fieldArgDefinition;
    public function __construct(Field $field, string $name)
    {
        $this->field = $field;
        $this->name = $name;
        // Extract all the properties from the fieldRegistry
        $fieldRegistry = FieldRegistryFacade::getInstance();
        $fieldID = $field->getID();
        $fieldDefinition = $fieldRegistry->getFieldDefinition($fieldID);
        $this->fieldArgDefinition = $fieldDefinition[SchemaDefinition::ARGNAME_ARGS][$name];
    }
    public function getID()
    {
        return FieldUtils::getInputValueID($this->field, $this->name);
    }
    public function getField(): Field
    {
        return $this->field;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getType(): AbstractType
    {
        $type = $this->fieldArgDefinition[SchemaDefinition::ARGNAME_TYPE];
        return $this->field->getTypeFromTypeName($type);
    }
    public function getDescription(): ?string
    {
        return $this->fieldArgDefinition[SchemaDefinition::ARGNAME_DESCRIPTION];
    }
    public function getDefaultValue(): ?string
    {
        return $this->fieldArgDefinition[SchemaDefinition::ARGNAME_DEFAULT_VALUE];
    }
}
