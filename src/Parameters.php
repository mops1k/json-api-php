<?php
declare(strict_types=1);

namespace JsonApi;

use JsonApi\Exception\InvalidParameterException;

class Parameters
{
    /**
     * @var array
     */
    protected $input;

    /**
     * @param array $input
     */
    public function __construct(array $input)
    {
        $this->input = $input;
    }

    /**
     * Get the includes.
     *
     * @param array $available
     *
     * @return array
     * @throws InvalidParameterException
     *
     */
    public function getInclude(array $available = []): array
    {
        if ($include = $this->getInput('include')) {
            $relationships = \explode(',', $include);

            $invalid = \array_diff($relationships, $available);

            if (\count($invalid)) {
                throw new InvalidParameterException(
                    \sprintf('Invalid includes [%s]', \implode(',', $invalid)),
                    1,
                    null,
                    'include'
                );
            }

            return $relationships;
        }

        return [];
    }

    /**
     * Get number of offset.
     *
     * @param int|null $perPage
     *
     * @return int
     *
     * @throws InvalidParameterException
     */
    public function getOffset(?int $perPage = null): int
    {
        if ($perPage && ($offset = $this->getOffsetFromNumber($perPage))) {
            return $offset;
        }

        $offset = (int) $this->getPage('offset');

        if ($offset < 0) {
            throw new InvalidParameterException('page[offset] must be >=0', 2, null, 'page[offset]');
        }

        return $offset;
    }

    /**
     * Calculate the offset based on the page[number] parameter.
     *
     * @param int $perPage
     *
     * @return int
     */
    protected function getOffsetFromNumber(int $perPage): int
    {
        $page = (int) $this->getPage('number');

        if ($page <= 1) {
            return 0;
        }

        return ($page - 1) * $perPage;
    }

    /**
     * Get the limit.
     *
     * @param int|null $max
     *
     * @return int|null
     */
    public function getLimit(?int $max = null): ?int
    {
        $limit = (int) $this->getPage('limit') ?: (int) $this->getPage('size') ?: null;

        if ($limit && $max) {
            $limit = \min($max, $limit);
        }

        return $limit;
    }

    /**
     * Get the sort.
     *
     * @param array $available
     *
     * @return array
     *
     * @throws InvalidParameterException
     */
    public function getSort(array $available = []): array
    {
        $sort = [];

        if ($input = $this->getInput('sort')) {
            $fields = \explode(',', $input);

            foreach ($fields as $field) {
                if (\strpos($field, '-') === 0) {
                    $field = \substr($field, 1);
                    $order = 'desc';
                } else {
                    $order = 'asc';
                }

                $sort[$field] = $order;
            }

            $invalid = \array_diff(\array_keys($sort), $available);

            if (\count($invalid)) {
                throw new InvalidParameterException(
                    \sprintf('Invalid sort fields [%s]', \implode(',', $invalid)),
                    3,
                    null,
                    'sort'
                );
            }
        }

        return $sort;
    }

    /**
     * Get the fields requested for inclusion.
     *
     * @return array
     */
    public function getFields(): array
    {
        $fields = $this->getInput('fields');

        if (!\is_array($fields)) {
            return [];
        }

        $results = [];
        foreach ($fields as $key => $field) {
            $results[$key] = \explode(',', $field);
        }

        return $results;
    }

    /**
     * Get a filter item.
     *
     * @return mixed
     */
    public function getFilter()
    {
        return $this->getInput('filter');
    }

    /**
     * Get an input item.
     *
     * @param string     $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    protected function getInput(string $key, $default = null)
    {
        return $this->input[$key] ?? $default;
    }

    /**
     * Get the page.
     *
     * @param string $key
     *
     * @return string
     */
    protected function getPage(string $key): string
    {
        $page = $this->getInput('page');

        return (string) ($page[$key] ?? '');
    }
}
