<?php
namespace PoP\GraphQL\ObjectModels;

class ObjectType extends AbstractType
{
    public function getKind()
    {
        return TypeKinds::OBJECT;
    }
}
