<?php
namespace PoP\GraphQL\ObjectModels;

abstract class AbstractType
{
    public function getID()
    {
        return $this->getKind();
    }
    abstract public function getKind();
}
