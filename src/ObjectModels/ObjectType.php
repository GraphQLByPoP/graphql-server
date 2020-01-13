<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\API\Schema\SchemaDefinition;
use PoP\GraphQL\ObjectModels\AbstractType;
use PoP\GraphQL\ObjectModels\InterfaceType;
use PoP\GraphQL\ObjectModels\HasFieldsTypeInterface;
use PoP\GraphQL\Facades\Registries\TypeRegistryFacade;
use PoP\GraphQL\Facades\Registries\FieldRegistryFacade;
use PoP\GraphQL\ObjectModels\HasInterfacesTypeInterface;
use PoP\GraphQL\Facades\Registries\InterfaceRegistryFacade;
use PoP\ComponentModel\Facades\Container\ObjectDictionaryFacade;

class ObjectType extends AbstractType implements HasFieldsTypeInterface, HasInterfacesTypeInterface
{
    protected $fields;
    protected $interfaces;
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
            $this->fields[FieldUtils::getID($this, $field)] = $fieldDefinition;
        }

        // Register the interfaces in the registry
        $interfaceRegistry = InterfaceRegistryFacade::getInstance();
        // $objectDictionary = ObjectDictionaryFacade::getInstance();
        $interfaceDefinitions = $typeDefinition[SchemaDefinition::ARGNAME_INTERFACES];
        $this->interfaces = [];
        foreach ($interfaceDefinitions as $interfaceResolverClass => $interfaceDefinition) {
            $interfaceName = $interfaceDefinition[SchemaDefinition::ARGNAME_NAME];
            // if (!$objectDictionary->has(InterfaceType::class, $interfaceName)) {
            //     $objectDictionary->set(InterfaceType::class, $interfaceName, new InterfaceType($interfaceName));
            // }
            // $interfaceTypeObject = $objectDictionary->get(InterfaceType::class, $interfaceName);
            // $interfaceRegistry->registerInterface($interfaceTypeObject, $interfaceName, $interfaceDefinition);
            $interfaceRegistry->registerType($interfaceName, $interfaceResolverClass, $interfaceDefinition);
            $this->interfaces[$interfaceName] = $interfaceDefinition;
        }
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
    // public function getInterfaces(): array
    // {
    //     $interfaces = [];
    //     // For each interface name, create a new InterfaceType object, if it doesn't already exist
    //     $objectDictionary = ObjectDictionaryFacade::getInstance();
    //     foreach ($this->typeDefinition[SchemaDefinition::ARGNAME_INTERFACES] as $interfaceName => $interfaceDefinition) {
    //         if (!$objectDictionary->has(InterfaceType::class, $interfaceName)) {
    //             $objectDictionary->set(InterfaceType::class, $interfaceName, new InterfaceType($interfaceName));
    //         }
    //         $interfaces[] = $objectDictionary->get(InterfaceType::class, $interfaceName);
    //     }
    //     return $interfaces;
    // }
}
