<?php
declare(strict_types=1);

namespace JsonApi;

trait LinksTrait
{
    /**
     * The links array.
     *
     * @var array
     */
    protected $links = [];

    /**
     * Get the links.
     *
     * @return array
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * Set the links.
     *
     * @param array $links
     *
     * @return $this
     */
    public function setLinks(array $links)
    {
        $this->links = $links;

        return $this;
    }

    /**
     * Add a link.
     *
     * @param string $key
     * @param string $value
     *
     * @return $this
     */
    public function addLink($key, $value)
    {
        $this->links[$key] = $value;

        return $this;
    }

    /**
     * Add pagination links (first, prev, next, and last).
     *
     * @param string $url The base URL for pagination links.
     * @param array $queryParams The query params provided in the request.
     * @param int $offset The current offset.
     * @param int $limit The current limit.
     * @param int|null $total The total number of results, or null if unknown.
     *
     * @return void
     */
    public function addPaginationLinks(string $url, array $queryParams, int $offset, int $limit, ?int $total = null): void
    {
        if (isset($queryParams['page']['number'])) {
            $offset = \floor($offset / $limit) * $limit;
        }

        $this->addPaginationLink('first', $url, $queryParams, 0, $limit);

        if ($offset > 0) {
            $this->addPaginationLink('prev', $url, $queryParams, (int) \max(0, $offset - $limit), $limit);
        }

        if ($total === null || $offset + $limit < $total) {
            $this->addPaginationLink('next', $url, $queryParams, (int) ($offset + $limit), $limit);
        }

        if ($total) {
            $this->addPaginationLink('last', $url, $queryParams, (int) \floor(($total - 1) / $limit) * $limit, $limit);
        }
    }

    /**
     * Add a pagination link.
     *
     * @param string $name The name of the link.
     * @param string $url The base URL for pagination links.
     * @param array $queryParams The query params provided in the request.
     * @param int $offset The offset to link to.
     * @param int $limit The current limit.
     *
     * @return void
     */
    protected function addPaginationLink(string $name, string $url, array $queryParams, int $offset, int $limit): void
    {
        if (!isset($queryParams['page']) || !is_array($queryParams['page'])) {
            $queryParams['page'] = [];
        }

        $page = &$queryParams['page'];

        if (isset($page['number'])) {
            $page['number'] = floor($offset / $limit) + 1;

            if ($page['number'] <= 1) {
                unset($page['number']);
            }
        } else {
            $page['offset'] = $offset;

            if ($page['offset'] <= 0) {
                unset($page['offset']);
            }
        }

        if (isset($page['limit'])) {
            $page['limit'] = $limit;
        }

        $queryString = \http_build_query($queryParams);

        $this->addLink($name, $url . ($queryString ? '?' . $queryString : ''));
    }
}
