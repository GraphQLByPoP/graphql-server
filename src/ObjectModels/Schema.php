<?php
namespace PoP\GraphQL\ObjectModels;

class Schema
{
    public const ID = 'schema';

    public function getID() {
        return self::ID;
    }
}
