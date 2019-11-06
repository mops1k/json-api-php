<?php
declare(strict_types=1);

namespace JsonApi;

class PolymorphicResource extends Resource
{
    /**
     * @param mixed $data
     * @param SerializerRegistryInterface $serializers
     */
    public function __construct($data, SerializerRegistryInterface $serializers)
    {
        parent::__construct($data, $serializers->getFromSerializable($data));
    }
}
