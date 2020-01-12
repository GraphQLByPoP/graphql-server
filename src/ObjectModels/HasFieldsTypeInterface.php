<?php
namespace PoP\GraphQL\ObjectModels;

interface HasFieldsTypeInterface
{
    public function getFields(bool $includeDeprecated = false): array;
}
