<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\API\Schema\SchemaDefinition;
use PoP\GraphQL\ObjectModels\AbstractType;
use PoP\GraphQL\ObjectModels\HasFieldsTypeTrait;
use PoP\GraphQL\ObjectModels\HasFieldsTypeInterface;
use PoP\GraphQL\Facades\Registries\TypeRegistryFacade;
use PoP\GraphQL\ObjectModels\HasInterfacesTypeInterface;
use PoP\GraphQL\Facades\Registries\InterfaceRegistryFacade;

class ObjectType extends AbstractType implements HasFieldsTypeInterface, HasInterfacesTypeInterface
{
    use HasFieldsTypeTrait;

    protected $fields;
    protected $interfaces;
    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->initFields($name);

        // Extract all the properties from the typeRegistry
        $typeRegistry = TypeRegistryFacade::getInstance();
        $typeDefinition = $typeRegistry->getTypeDefinition($name);

        // Register the interfaces in the registry
        $interfaceRegistry = InterfaceRegistryFacade::getInstance();
        $interfaceDefinitions = $typeDefinition[SchemaDefinition::ARGNAME_INTERFACES];
        $this->interfaces = [];
        foreach ($interfaceDefinitions as $interfaceResolverClass => $interfaceDefinition) {
            $interfaceName = $interfaceDefinition[SchemaDefinition::ARGNAME_NAME];
            $interfaceRegistry->registerType($interfaceName, $interfaceResolverClass, $interfaceDefinition);
            $this->interfaces[$interfaceName] = $interfaceDefinition;
        }
    }

    protected function getFieldDefinitions(string $name)
    {
        // Extract all the properties from the typeRegistry
        $typeRegistry = TypeRegistryFacade::getInstance();
        $typeDefinition = $typeRegistry->getTypeDefinition($name);
        // Include the global fields and the ones specific to this type
        return array_merge(
            $typeRegistry->getGlobalFields(),
            $typeDefinition[SchemaDefinition::ARGNAME_FIELDS]
        );
    }

    public function getKind()
    {
        return TypeKinds::OBJECT;
    }

    public function getTypeDefinition(string $name): array
    {
        $typeRegistry = TypeRegistryFacade::getInstance();
        return $typeRegistry->getTypeDefinition($name);
    }

    public function getFields(bool $includeDeprecated = false): array
    {
        if ($includeDeprecated) {
            // Include all fields
            $fields = $this->fields;
        } else {
            // Filter out the deprecated fields
            $fields = array_filter(
                $this->fields,
                function($fieldDefinition) {
                    return !$fieldDefinition[SchemaDefinition::ARGNAME_DEPRECATED];
                }
            );
        }
        return array_keys($fields);
    }

    public function getInterfaces(): array
    {
        return array_keys($this->interfaces);
    }
}
