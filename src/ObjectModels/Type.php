<?php
namespace PoP\GraphQL\ObjectModels;

class Type
{
    private $name;
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    public function getID() {
        return $this->getName();
    }
    public function getName() {
        return $this->name;
    }
}
