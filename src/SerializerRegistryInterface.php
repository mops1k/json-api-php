<?php
declare(strict_types=1);

namespace JsonApi;

interface SerializerRegistryInterface
{
    /**
     * Instantiate serializer from the serializable object.
     *
     * @param object $serializable
     * @return SerializerInterface
     */
    public function getFromSerializable($serializable): SerializerInterface;
}
