<?php

declare(strict_types=1);

namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\NonDocumentableTypeTrait;
use PoP\GraphQL\ObjectModels\AbstractNestableType;

class ListType extends AbstractNestableType
{
    use NonDocumentableTypeTrait;

    public function getName(): string
    {
        return sprintf(
            '[%s]',
            $this->nestedType->getName()
        );
    }

    public function getKind(): string
    {
        return TypeKinds::LIST;
    }
}
