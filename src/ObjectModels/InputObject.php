<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\AbstractType;

class InputObject
{
    protected $type;
    protected $field;
    protected $name;
    protected $description;
    protected $defaultValue;
    public function __construct(AbstractType $type, string $field, string $name, ?string $description = null, ?string $defaultValue = null)
    {
        $this->type = $type;
        $this->field = $field;
        $this->name = $name;
        $this->description = $description;
        $this->defaultValue = $defaultValue;
    }
    public function getID()
    {
        return FieldUtils::getInputObjectID($this->type, $this->field, $this->name);
    }
    public function getType(): AbstractType
    {
        return $this->type;
    }
    public function getField(): string
    {
        return $this->field;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function getDefaultValue(): ?string
    {
        return $this->defaultValue;
    }
}
