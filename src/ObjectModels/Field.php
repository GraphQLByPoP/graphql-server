<?php
namespace PoP\GraphQL\ObjectModels;

use PoP\API\Schema\SchemaDefinition;
use PoP\GraphQL\ObjectModels\AbstractType;
use PoP\GraphQL\Facades\Registries\FieldRegistryFacade;

class Field
{
    protected $type;
    protected $name;
    protected $description;
    public function __construct(AbstractType $type, string $name)
    {
        $this->type = $type;
        $this->name = $name;

        // Extract all the properties from the typeRegistry
        $fieldRegistry = FieldRegistryFacade::getInstance();
        $id = $this->getID();
        $fieldDefinition = $fieldRegistry->getFieldDefinition($id);
        $this->description = $fieldDefinition[SchemaDefinition::ARGNAME_DESCRIPTION];
    }
    public function getID()
    {
        return FieldUtils::getID($this->type, $this->name);
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getType(): AbstractType
    {
        return $this->type;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
}
