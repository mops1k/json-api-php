<?php
declare(strict_types=1);

namespace JsonApi;

use JsonSerializable;

class Document implements JsonSerializable
{
    use LinksTrait;
    use MetaTrait;

    public const MEDIA_TYPE = 'application/vnd.api+json';

    /**
     * The included array.
     *
     * @var array
     */
    protected $included = [];

    /**
     * The errors array.
     *
     * @var array
     */
    protected $errors;

    /**
     * The json-api array.
     *
     * @var array
     */
    protected $jsonApi;

    /**
     * The data object.
     *
     * @var ElementInterface
     */
    protected $data;

    /**
     * @param ElementInterface $data
     */
    public function __construct(ElementInterface $data = null)
    {
        $this->data = $data;
    }

    /**
     * Get included resources.
     *
     * @param ElementInterface $element
     * @param bool             $includeParent
     *
     * @return Resource[]
     */
    protected function getIncluded(ElementInterface $element, bool $includeParent = false): array
    {
        $included = [];

        foreach ($element->getResources() as $resource) {
            if ($resource->isIdentifier()) {
                continue;
            }

            if ($includeParent) {
                $included = $this->mergeResource($included, $resource);
            } else {
                $type = $resource->getType();
                $id = $resource->getId();
            }

            foreach ($resource->getUnfilteredRelationships() as $relationship) {
                $includedElement = $relationship->getData();

                if (!$includedElement instanceof ElementInterface) {
                    continue;
                }

                foreach ($this->getIncluded($includedElement, true) as $child) {
                    // If this resource is the same as the top-level "data"
                    // resource, then we don't want it to show up again in the
                    // "included" array.
                    if (!$includeParent && $child->getType() === $type && $child->getId() === $id) {
                        continue;
                    }

                    $included = $this->mergeResource($included, $child);
                }
            }
        }

        $flattened = [];

        \array_walk_recursive($included, function ($a) use (&$flattened) {
            $flattened[] = $a;
        });

        return $flattened;
    }

    /**
     * @param \JsonApi\Resource[] $resources
     * @param \JsonApi\Resource   $newResource
     *
     * @return \JsonApi\Resource[]
     */
    protected function mergeResource(array $resources, \JsonApi\Resource $newResource): array
    {
        $type = $newResource->getType();
        $id = $newResource->getId();

        if (isset($resources[$type][$id])) {
            $resources[$type][$id]->merge($newResource);
        } else {
            $resources[$type][$id] = $newResource;
        }

        return $resources;
    }

    /**
     * Set the data object.
     *
     * @param ElementInterface $element
     *
     * @return $this
     */
    public function setData(ElementInterface $element)
    {
        $this->data = $element;

        return $this;
    }

    /**
     * Set the errors array.
     *
     * @param array $errors
     *
     * @return $this
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * Set the json-api array.
     *
     * @param array $jsonApi
     *
     * @return $this
     */
    public function setJsonApi(array $jsonApi)
    {
        $this->jsonApi = $jsonApi;

        return $this;
    }

    /**
     * Map everything to arrays.
     *
     * @return array
     */
    public function toArray(): array
    {
        $document = [];

        if (!empty($this->links)) {
            $document['links'] = $this->links;
        }

        if (!empty($this->data)) {
            $document['data'] = $this->data->toArray();

            $resources = $this->getIncluded($this->data);

            if (\count($resources)) {
                $document['included'] = \array_map(function (Resource $resource) {
                    return $resource->toArray();
                }, $resources);
            }
        }

        if (!empty($this->meta)) {
            $document['meta'] = $this->meta;
        }

        if (!empty($this->errors)) {
            $document['errors'] = $this->errors;
        }

        if (!empty($this->jsonApi)) {
            $document['jsonapi'] = $this->jsonApi;
        }

        return $document;
    }

    /**
     * Map to string.
     *
     * @return string
     */
    public function __toString()
    {
        return \json_encode($this->toArray());
    }

    /**
     * Serialize for JSON usage.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
