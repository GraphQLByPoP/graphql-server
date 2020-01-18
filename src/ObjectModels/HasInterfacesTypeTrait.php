<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\API\Schema\SchemaDefinition;
use PoP\GraphQL\ObjectModels\InterfaceType;
use PoP\GraphQL\SchemaDefinition\SchemaDefinitionHelpers;
use PoP\GraphQL\Facades\Registries\SchemaDefinitionReferenceRegistryFacade;

trait HasInterfacesTypeTrait
{
    protected $interfaces;
    /**
     * Reference the already-registered interfaces
     *
     * @return void
     */
    protected function initInterfaces(array &$fullSchemaDefinition, array $schemaDefinitionPath): void
    {
        $this->interfaces = [];
        $interfaceSchemaDefinitionPath = array_merge(
            $schemaDefinitionPath,
            [
                SchemaDefinition::ARGNAME_INTERFACES,
            ]
        );
        $schemaDefinitionReferenceRegistry = SchemaDefinitionReferenceRegistryFacade::getInstance();
        $interfaceSchemaDefinitionPointer = SchemaDefinitionHelpers::advancePointerToPath($fullSchemaDefinition, $interfaceSchemaDefinitionPath);
        foreach ($interfaceSchemaDefinitionPointer as $interfaceName) {
            // The InterfaceType has already been registered on the root, under "interfaces"
            $schemaDefinitionID = SchemaDefinitionHelpers::getID(
                [
                    SchemaDefinition::ARGNAME_INTERFACES,
                    $interfaceName
                ]
            );
            $this->interfaces[] = $schemaDefinitionReferenceRegistry->getSchemaDefinitionReference($schemaDefinitionID);
        }
    }

    public function getInterfaces(): array
    {
        return $this->interfaces;
    }
    public function getInterfaceIDs(): array
    {
        return array_map(
            function(InterfaceType $interfaceType) {
                return $interfaceType->getID();
            },
            $this->getInterfaces()
        );
    }
}
