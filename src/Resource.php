<?php
declare(strict_types=1);

namespace JsonApi;

class Resource implements ElementInterface
{
    use LinksTrait;
    use MetaTrait;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * A list of relationships to include.
     *
     * @var array
     */
    protected $includes = [];

    /**
     * A list of fields to restrict to.
     *
     * @var array|null
     */
    protected $fields;

    /**
     * An array of Resources that should be merged into this one.
     *
     * @var Resource[]
     */
    protected $merged = [];

    /**
     * @var Relationship[]
     */
    private $relationships;

    /**
     * @param mixed               $data
     * @param SerializerInterface $serializer
     */
    public function __construct($data, SerializerInterface $serializer)
    {
        $this->data       = $data;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function getResources(): array
    {
        return [$this];
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        $array = $this->toIdentifier();

        if (!$this->isIdentifier()) {
            $attributes = $this->getAttributes();
            if ($attributes) {
                $array['attributes'] = $attributes;
            }
        }

        $relationships = $this->getRelationshipsAsArray();

        if (count($relationships)) {
            $array['relationships'] = $relationships;
        }

        $links = [];
        if (!empty($this->links)) {
            $links = $this->links;
        }
        $serializerLinks = $this->serializer->getLinks($this->data);
        if (!empty($serializerLinks)) {
            $links = array_merge($serializerLinks, $links);
        }
        if (!empty($links)) {
            $array['links'] = $links;
        }

        $meta = [];
        if (!empty($this->meta)) {
            $meta = $this->meta;
        }
        $serializerMeta = $this->serializer->getMeta($this->data);
        if (!empty($serializerMeta)) {
            $meta = array_merge($serializerMeta, $meta);
        }
        if (!empty($meta)) {
            $array['meta'] = $meta;
        }

        return $array;
    }

    /**
     * Check whether or not this resource is an identifier (i.e. does it have
     * any data attached?).
     *
     * @return bool
     */
    public function isIdentifier(): bool
    {
        return !is_object($this->data) && !is_array($this->data);
    }

    /**
     * {@inheritdoc}
     */
    public function toIdentifier(): ?array
    {
        if (!$this->data) {
            return null;
        }

        $array = [
            'type' => $this->getType(),
            'id' => $this->getId()
        ];

        if (!empty($this->meta)) {
            $array['meta'] = $this->meta;
        }

        return $array;
    }

    /**
     * Get the resource type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->serializer->getType($this->data);
    }

    /**
     * Get the resource ID.
     *
     * @return string
     */
    public function getId(): string
    {
        if (!is_object($this->data) && !is_array($this->data)) {
            return (string)$this->data;
        }

        return $this->serializer->getId($this->data);
    }

    /**
     * Get the resource attributes.
     *
     * @return array
     */
    public function getAttributes(): array
    {
        $attributes = $this->serializer->getAttributes($this->data, $this->getOwnFields());

        $attributes = $this->filterFields($attributes);

        $attributes = $this->mergeAttributes($attributes);

        return $attributes;
    }

    /**
     * Get the requested fields for this resource type.
     *
     * @return array|null
     */
    protected function getOwnFields(): ?array
    {
        $type = $this->getType();

        return $this->fields[$type] ?? null;
    }

    /**
     * Filter the given fields array (attributes or relationships) according
     * to the requested fieldset.
     *
     * @param array $fields
     *
     * @return array
     */
    protected function filterFields(array $fields): array
    {
        if ($requested = $this->getOwnFields()) {
            $fields = \array_intersect_key($fields, \array_flip($requested));
        }

        return $fields;
    }

    /**
     * Merge the attributes of merged resources into an array of attributes.
     *
     * @param array $attributes
     *
     * @return array
     */
    protected function mergeAttributes(array $attributes): array
    {
        $results = [];
        foreach ($this->merged as $resource) {
            $results[] = $resource->getAttributes();
        }

        return \array_replace_recursive($attributes, ...$results);
    }

    /**
     * Get the resource relationships.
     *
     * @return Relationship[]
     */
    public function getRelationships(): array
    {
        return $this->filterFields($this->getUnfilteredRelationships());
    }

    /**
     * Get the resource relationships without considering requested ones.
     *
     * @return Relationship[]|array
     */
    public function getUnfilteredRelationships(): array
    {
        if (isset($this->relationships)) {
            return $this->relationships;
        }

        $paths = Util::parseRelationshipPaths($this->includes);

        $relationships = [];

        foreach ($paths as $name => $nested) {
            $relationship = $this->serializer->getRelationship($this->data, $name);

            if ($relationship) {
                $relationshipData = $relationship->getData();
                if ($relationshipData instanceof ElementInterface) {
                    $relationshipData->with($nested)->fields($this->fields);
                }

                $relationships[$name] = $relationship;
            }
        }

        return $this->relationships = $relationships;
    }

    /**
     * Get the resource relationships as an array.
     *
     * @return array
     */
    public function getRelationshipsAsArray(): array
    {
        $relationships = $this->getRelationships();

        $relationships = $this->convertRelationshipsToArray($relationships);

        return $this->mergeRelationships($relationships);
    }

    /**
     * Merge the relationships of merged resources into an array of
     * relationships.
     *
     * @param array $relationships
     *
     * @return array
     */
    protected function mergeRelationships(array $relationships): array
    {
        $results = [];
        foreach ($this->merged as $key => $resource) {
            $results[$key] = $resource->getRelationshipsAsArray();
        }

        return \array_replace_recursive($relationships, ...$results);
    }

    /**
     * Convert the given array of Relationship objects into an array.
     *
     * @param Relationship[] $relationships
     *
     * @return array
     */
    protected function convertRelationshipsToArray(array $relationships): array
    {
        $results = [];
        foreach ($relationships as $key => $relationship) {
            $results[$key] = $relationship->toArray();
        }

        return $results;
    }

    /**
     * Merge a resource into this one.
     *
     * @param Resource $resource
     *
     * @return void
     */
    public function merge(Resource $resource): void
    {
        $this->merged[] = $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function with(array $relationships)
    {
        $this->includes = \array_unique(\array_merge($this->includes, $relationships));

        $this->relationships = null;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function fields(?array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return SerializerInterface
     */
    public function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

    /**
     * @param SerializerInterface $serializer
     *
     * @return $this
     */
    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;

        return $this;
    }
}
