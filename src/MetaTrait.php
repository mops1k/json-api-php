<?php
declare(strict_types=1);

namespace JsonApi;

trait MetaTrait
{
    /**
     * The meta data array.
     *
     * @var array
     */
    protected $meta;

    /**
     * Get the meta.
     *
     * @return array
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    /**
     * Set the meta data array.
     *
     * @param array $meta
     *
     * @return $this
     */
    public function setMeta(array $meta)
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * Add meta data.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function addMeta(string $key, $value)
    {
        $this->meta[$key] = $value;

        return $this;
    }
}
