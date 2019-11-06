<?php
declare(strict_types=1);

namespace JsonApi\Tests\Stubs\Serializers;

use JsonApi\AbstractSerializer;
use JsonApi\PolymorphicCollection;
use JsonApi\Relationship;
use JsonApi\Tests\Stubs\Serializables\Garage;

class GarageSerializer extends AbstractSerializer
{
    protected $type = 'garages';

    public function getAttributes($garage, array $fields = null): array
    {
        return [];
    }

    public function vehicles(Garage $garage): Relationship
    {
        $element = new PolymorphicCollection(
            $garage->vehicles,
            new VehicleSerializerRegistry()
        );

        return new Relationship($element);
    }
}
