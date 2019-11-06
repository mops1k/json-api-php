<?php
declare(strict_types=1);

namespace JsonApi\Tests\Stubs\Serializers;

use JsonApi\AbstractSerializerRegistry;
use JsonApi\Tests\Stubs\Serializables\Bike;
use JsonApi\Tests\Stubs\Serializables\Car;

class VehicleSerializerRegistry extends AbstractSerializerRegistry
{
    protected $serializers = [
        Car::class => CarSerializer::class,
        Bike::class => BikeSerializer::class,
    ];
}
