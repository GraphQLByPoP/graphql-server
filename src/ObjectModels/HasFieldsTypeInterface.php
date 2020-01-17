<?php
namespace PoP\GraphQL\ObjectModels;

interface HasFieldsTypeInterface
{
    public function getFields(bool $includeDeprecated = false): array;
    public function getFieldIDs(bool $includeDeprecated = false): array;
}
