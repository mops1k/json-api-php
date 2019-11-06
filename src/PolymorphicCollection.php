<?php
declare(strict_types=1);

namespace JsonApi;

class PolymorphicCollection implements ElementInterface
{
    /**
     * @var array
     */
    protected $resources = [];

    /**
     * Create a new collection instance.
     *
     * @param mixed $data
     * @param SerializerRegistryInterface $serializers
     */
    public function __construct($data, SerializerRegistryInterface $serializers)
    {
        $this->resources = $this->buildResources($data, $serializers);
    }

    /**
     * Convert an array of raw data to Resource objects.
     *
     * @param mixed $data
     * @param SerializerRegistryInterface $serializers
     * @return Resource[]
     */
    protected function buildResources($data, SerializerRegistryInterface $serializers): array
    {
        $resources = [];

        foreach ($data as $resource) {
            if (!$resource instanceof Resource) {
                $resource = new Resource($resource, $serializers->getFromSerializable($resource));
            }

            $resources[] = $resource;
        }

        return $resources;
    }

    /**
     * {@inheritdoc}
     */
    public function getResources(): array
    {
        return $this->resources;
    }

    /**
     * Set the resources array.
     *
     * @param array $resources
     *
     * @return $this
     */
    public function setResources($resources)
    {
        $this->resources = $resources;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function with(array $relationships)
    {
        foreach ($this->resources as $resource) {
            $resource->with($relationships);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function fields(?array $fields)
    {
        foreach ($this->resources as $resource) {
            $resource->fields($fields);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return \array_map(function (Resource $resource) {
            return $resource->toArray();
        }, $this->resources);
    }

    /**
     * {@inheritdoc}
     */
    public function toIdentifier(): array
    {
        return \array_map(function (Resource $resource) {
            return $resource->toIdentifier();
        }, $this->resources);
    }
}
