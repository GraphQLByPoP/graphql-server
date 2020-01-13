<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\API\Schema\SchemaDefinition;
use PoP\GraphQL\Syntax\SyntaxHelpers;
use PoP\GraphQL\ObjectModels\UnionType;
use PoP\GraphQL\ObjectModels\NonNullType;
use PoP\GraphQL\ObjectModels\AbstractType;
use PoP\GraphQL\ObjectModels\AbstractResolvableType;
use PoP\GraphQL\Facades\Registries\FieldRegistryFacade;
use PoP\GraphQL\Facades\Registries\TypeRegistryFacade;

class Field
{
    /**
     * The type to which the field belongs
     *
     * @var AbstractResolvableType
     */
    protected $type;
    /**
     * The field name
     *
     * @var string
     */
    protected $name;
    /**
     * Definition for the field
     *
     * @var array
     */
    protected $fieldDefinition;
    public function __construct(AbstractResolvableType $type, string $name)
    {
        $this->type = $type;
        $this->name = $name;

        // Extract all the properties from the typeRegistry
        $fieldRegistry = FieldRegistryFacade::getInstance();
        $id = $this->getID();
        $this->fieldDefinition = $fieldRegistry->getFieldDefinition($id);
    }
    public function getID()
    {
        return FieldUtils::getID($this->type, $this->name);
    }
    public function getName(): string
    {
        return $this->name;
    }
    /**
     * This is not to what type the field belongs to, but to what type the field is resolved to
     * Eg: field "references" in type "Post" belongs to type "Post" but is resolved to type "UnionPost"
     *
     * @return AbstractType
     */
    public function getType(): AbstractType
    {
        // The type to which the field resolves to
        $type = $this->fieldDefinition[SchemaDefinition::ARGNAME_TYPE];

        // Check if it is non-null
        if (SyntaxHelpers::isNonNullType($type)) {
            return new NonNullType(SyntaxHelpers::getNonNullTypeNestedTypes($type));
        }

        // Check if it is an array
        if (SyntaxHelpers::isListType($type)) {
            return new ListType(SyntaxHelpers::getListTypeNestedTypes($type));
        }

        // Check if it is an enum type
        if ($type == SchemaDefinition::TYPE_ENUM) {
            $enumValues = $this->fieldDefinition[SchemaDefinition::ARGNAME_ENUMVALUES];
            return new EnumType($enumValues);
        }

        // Check if it is any scalar
        $scalarTypes = [
            SchemaDefinition::TYPE_OBJECT,
            SchemaDefinition::TYPE_MIXED,
            SchemaDefinition::TYPE_STRING,
            SchemaDefinition::TYPE_INT,
            SchemaDefinition::TYPE_FLOAT,
            SchemaDefinition::TYPE_BOOL,
            SchemaDefinition::TYPE_DATE,
            SchemaDefinition::TYPE_TIME,
            SchemaDefinition::TYPE_URL,
            SchemaDefinition::TYPE_EMAIL,
            SchemaDefinition::TYPE_IP,

        ];
        if (in_array($type, $scalarTypes)) {
            return new ScalarType($type);
        }

        // Otherwise, it's either a Union or an Object. Find out from the TypeRegistry
        $typeRegistry = TypeRegistryFacade::getInstance();
        $typeDefinition = $typeRegistry->getTypeDefinition($type);
        if ($typeDefinition[SchemaDefinition::ARGNAME_IS_UNION]) {
            return new UnionType($type);
        }
        return new ObjectType($type);
    }
    public function getDescription(): ?string
    {
        return $this->fieldDefinition[SchemaDefinition::ARGNAME_DESCRIPTION];
    }
}
