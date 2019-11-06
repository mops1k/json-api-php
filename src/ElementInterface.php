<?php
declare(strict_types=1);

namespace JsonApi;

interface ElementInterface
{
    /**
     * Get the resources array.
     *
     * @return Resource[]|array
     */
    public function getResources(): array;

    /**
     * Map to a "resource object" array.
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * Map to a "resource object identifier" array.
     *
     * @return array|null
     */
    public function toIdentifier(): ?array;

    /**
     * Request a relationship to be included.
     *
     * @param array $relationships
     *
     * @return $this
     */
    public function with(array $relationships);

    /**
     * Request a restricted set of fields.
     *
     * @param array|null $fields
     *
     * @return $this
     */
    public function fields(?array $fields);
}
