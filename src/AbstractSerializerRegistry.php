<?php
declare(strict_types=1);

namespace JsonApi;

use RuntimeException;

abstract class AbstractSerializerRegistry implements SerializerRegistryInterface
{
    /**
     * @var array
     */
    protected $serializers = [];

    /**
     * @inheritDoc
     */
    public function getFromSerializable($serializable): SerializerInterface
    {
        $class = \get_class($serializable);

        if (! isset($this->serializers[$class])) {
            throw new RuntimeException(
                \sprintf("Serializer with name `%s` is not exists", $class)
            );
        }

        return new $this->serializers[$class]();
    }
}
