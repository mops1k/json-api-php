<?php
declare(strict_types=1);

namespace JsonApi;

class Collection implements ElementInterface
{
    /**
     * @var array
     */
    protected $resources = [];

    /**
     * Create a new collection instance.
     *
     * @param mixed               $data
     * @param SerializerInterface $serializer
     */
    public function __construct($data, SerializerInterface $serializer)
    {
        $this->resources = $this->buildResources($data, $serializer);
    }

    /**
     * Convert an array of raw data to Resource objects.
     *
     * @param mixed $data
     * @param SerializerInterface $serializer
     *
     * @return Resource[]
     */
    protected function buildResources($data, SerializerInterface $serializer)
    {
        $resources = [];

        foreach ($data as $resource) {
            if (!($resource instanceof Resource)) {
                $resource = new Resource($resource, $serializer);
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
     * Request a relationship to be included for all resources.
     *
     * @param array $relationships
     *
     * @return $this
     */
    public function with(array $relationships)
    {
        foreach ($this->resources as $resource) {
            $resource->with($relationships);
        }

        return $this;
    }

    /**
     * Request a restricted set of fields.
     *
     * @param array|null $fields
     *
     * @return $this
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
        $results = [];
        foreach ($this->resources as $resource) {
            $results[] = $resource->toArray();
        }

        return $results;
    }

    /**
     * {@inheritdoc}
     */
    public function toIdentifier(): array
    {
        $results = [];
        foreach ($this->resources as $resource) {
            $results[] = $resource->toIdentifier();
        }

        return $results;
    }
}
