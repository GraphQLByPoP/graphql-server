<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\GraphQL\ObjectModels\InterfaceType;
use PoP\API\Schema\SchemaDefinition;

trait HasInterfacesTypeTrait
{
    protected $interfaces;
    protected function initInterfaces(): void
    {
        $this->interfaces = [];
        // foreach ($this->schemaDefinitions[SchemaDefinition::ARGNAME_INTERFACES] as $interfaceResolverClass => $interfaceDefinition) {
        //     $interfaceName = $interfaceDefinition[SchemaDefinition::ARGNAME_NAME];
        //     $interfaces[] = new InterfaceType(
        //         TypeUtils::composeSchemaDefinitionPath($this->schemaDefinitionPath, [SchemaDefinition::ARGNAME_INTERFACES, $interfaceName])
        //     );
        // }
    }

    public function getInterfaces(): array
    {
        if (is_null($this->interfaces)) {
            $this->initInterfaces();
        }
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
