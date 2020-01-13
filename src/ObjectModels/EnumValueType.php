<?php
namespace PoP\GraphQL\ObjectModels;

class EnumValueType {
    protected $value;
    public function __construct($value)
    {
        $this->value = $value;
    }
    public function getID()
    {
        return $this->getName();
    }
    public function getValue()
    {
        return $this->value;
    }
    public function getName(): string
    {
        return (string)$this->getValue();
    }
    public function getDescription(): ?string
    {
        return null;
    }
    public function isDeprecated(): bool
    {
        return false;
    }
    public function getDeprecatedReason(): ?string
    {
        return null;
    }
}
