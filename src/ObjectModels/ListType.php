<?php

declare(strict_types=1);

namespace PoP\GraphQLServer\ObjectModels;

use PoP\GraphQLServer\ObjectModels\NonDocumentableTypeTrait;
use PoP\GraphQLServer\ObjectModels\AbstractNestableType;

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
