<?php
declare(strict_types=1);

namespace JsonApi\Tests\Stubs\Serializers;

use JsonApi\AbstractSerializer;

class BikeSerializer extends AbstractSerializer
{
    public const TYPE = 'bikes';

    public function getAttributes($bike, array $fields = null): array
    {
        return [
            'name' => $bike->name,
        ];
    }
}
