<?php
declare(strict_types=1);

namespace JsonApi\Tests;

use JsonApi\PolymorphicResource;
use JsonApi\Tests\AbstractTestCase;
use JsonApi\Tests\Stubs\Serializables\Bike;
use JsonApi\Tests\Stubs\Serializables\Car;
use JsonApi\Tests\Stubs\Serializers\VehicleSerializerRegistry;

class PolymorphicResourceTest extends AbstractTestCase
{
    public function testPolymorphicResourceType()
    {
        $car = new Car(123);
        $bike = new Bike(234);
        $serializerRegistry = new VehicleSerializerRegistry();

        $resource1 = new PolymorphicResource($car, $serializerRegistry);
        $resource2 = new PolymorphicResource($bike, $serializerRegistry);

        $this->assertSame('cars', $resource1->getType());
        $this->assertSame('bikes', $resource2->getType());
    }
}
