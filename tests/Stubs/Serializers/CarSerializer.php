<?php
declare(strict_types=1);

namespace JsonApi\Tests\Stubs\Serializers;

use JsonApi\AbstractSerializer;

class CarSerializer extends AbstractSerializer
{
    public const TYPE = 'cars';

    public function getAttributes($car, array $fields = null): array
    {
        return [
            'name' => $car->name,
        ];
    }
}
