<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\API\Schema\SchemaDefinition;
use PoP\GraphQL\Facades\Registries\TypeRegistryFacade;
use PoP\GraphQL\Facades\Registries\FieldRegistryFacade;

class ObjectType extends AbstractType implements HasFieldsTypeInterface
{
    protected $fields;
    public function __construct(string $name)
    {
        parent::__construct($name);

        // Extract all the properties from the typeRegistry
        $typeRegistry = TypeRegistryFacade::getInstance();
        $typeDefinition = $typeRegistry->getTypeDefinition($name);
        // Include the global fields and the ones specific to this type
        $fieldDefinitions = array_merge(
            $typeRegistry->getGlobalFields(),
            $typeDefinition[SchemaDefinition::ARGNAME_FIELDS]
        );

        // Add the type as part of the ID, and register them in the fieldRegistry
        $fieldRegistry = FieldRegistryFacade::getInstance();
        $this->fields = [];
        foreach ($fieldDefinitions as $field => $fieldDefinition) {
            $fieldRegistry->registerField($this, $field, $fieldDefinition);
            $this->fields[] = FieldUtils::getID($this, $field);
        }

        // var_dump('fields', $this->fields);
    }

    public function getKind()
    {
        return TypeKinds::OBJECT;
    }

    public function getFields(bool $includeDeprecated = false): array
    {
        return $this->fields;
    }
}
