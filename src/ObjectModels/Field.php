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
    public const SCALAR_TYPES = [
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
    /**
     * Field arguments
     *
     * @var array
     */
    protected $args;
    public function __construct(AbstractResolvableType $type, string $name)
    {
        $this->type = $type;
        $this->name = $name;

        // Extract all the properties from the fieldRegistry
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
        $typeName = $this->fieldDefinition[SchemaDefinition::ARGNAME_TYPE];
        return $this->getTypeFromTypeName($typeName);
    }
    public function getTypeFromTypeName(string $typeName): AbstractType
    {
        // Check if it is non-null
        if (SyntaxHelpers::isNonNullType($typeName)) {
            return new NonNullType(SyntaxHelpers::getNonNullTypeNestedTypes($typeName));
        }

        // Check if it is an array
        if (SyntaxHelpers::isListType($typeName)) {
            return new ListType(SyntaxHelpers::getListTypeNestedTypes($typeName));
        }

        // Check if it is an enum type
        if ($typeName == SchemaDefinition::TYPE_ENUM) {
            // $name = $this->fieldDefinition[SchemaDefinition::ARGNAME_NAME];
            return new EnumType($this->getID()/*, $name*/);
        }

        // Check if it is any scalar
        if (in_array($typeName, self::SCALAR_TYPES)) {
            return new ScalarType($typeName);
        }

        // Otherwise, it's either a Union or an Object. Find out from the TypeRegistry
        $typeRegistry = TypeRegistryFacade::getInstance();
        $typeDefinition = $typeRegistry->getTypeDefinition($typeName);
        if ($typeDefinition[SchemaDefinition::ARGNAME_IS_UNION]) {
            return new UnionType($typeName);
        }
        return new ObjectType($typeName);
    }
    public function getDescription(): ?string
    {
        return $this->fieldDefinition[SchemaDefinition::ARGNAME_DESCRIPTION];
    }
    /**
     * Implementation of "args" field from the Field object (https://graphql.github.io/graphql-spec/draft/#sel-FAJbLACsEIDuEAA-vb)
     *
     * @return array of InputValue type
     */
    public function getArgs(): array
    {
        if (is_null($this->args)) {
            $this->initArgs();
        }
        return $this->args;
    }
    protected function initArgs(): void
    {
        $this->args = [];
        foreach (array_keys($this->fieldDefinition[SchemaDefinition::ARGNAME_ARGS] ?? []) as $fieldArgName) {
            $this->args[] = new InputValue($this, $fieldArgName);
        }
    }
    public function getArgIDs(): array
    {
        return array_map(
            function($inputValue) {
                return FieldUtils::getInputValueID($inputValue->getField(), $inputValue->getName());
            },
            $this->getArgs()
        );
    }
}
