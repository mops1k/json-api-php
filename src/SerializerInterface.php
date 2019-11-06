<?php

namespace JsonApi;

interface SerializerInterface
{
    /**
     * Get the type.
     *
     * @param mixed $model
     *
     * @return string
     */
    public function getType($model): string;

    /**
     * Get the id.
     *
     * @param mixed $model
     *
     * @return string
     */
    public function getId($model): string;

    /**
     * Get the attributes array.
     *
     * @param mixed $model
     * @param array|null $fields
     *
     * @return array
     */
    public function getAttributes($model, array $fields = null): array;

    /**
     * Get the links array.
     *
     * @param mixed $model
     *
     * @return array
     */
    public function getLinks($model): array;

    /**
     * Get the meta.
     *
     * @param mixed $model
     *
     * @return array
     */
    public function getMeta($model): array;

    /**
     * Get a relationship.
     *
     * @param mixed $model
     * @param string $name
     *
     * @return Relationship|null
     */
    public function getRelationship($model, $name): ?Relationship;
}
