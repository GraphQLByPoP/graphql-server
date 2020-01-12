<?php
namespace PoP\GraphQL\ObjectModels;

class Directive
{
    protected $name;
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    public function getID()
    {
        return $this->getName();
    }
    public function getName(): string
    {
        return $this->name;
    }
}
