<?php
declare(strict_types=1);

namespace JsonApi;

class Relationship
{
    use LinksTrait;
    use MetaTrait;

    /**
     * The data object.
     *
     * @var ElementInterface|null
     */
    protected $data;

    /**
     * Create a new relationship.
     *
     * @param ElementInterface|null $data
     */
    public function __construct(?ElementInterface $data = null)
    {
        $this->data = $data;
    }

    /**
     * Get the data object.
     *
     * @return ElementInterface|null
     */
    public function getData(): ?ElementInterface
    {
        return $this->data;
    }

    /**
     * Set the data object.
     *
     * @param ElementInterface|null $data
     *
     * @return $this
     */
    public function setData(?ElementInterface $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Map everything to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $array = [];

        if (!empty($this->data)) {
            $array['data'] = $this->data->toIdentifier();
        }

        if (!empty($this->meta)) {
            $array['meta'] = $this->meta;
        }

        if (!empty($this->links)) {
            $array['links'] = $this->links;
        }

        return $array;
    }
}
