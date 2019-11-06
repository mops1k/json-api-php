<?php
declare(strict_types=1);

namespace JsonApi;

class Util
{
    /**
     * Parse relationship paths.
     *
     * Given a flat array of relationship paths like:
     *
     * ['user', 'user.employer', 'user.employer.country', 'comments']
     *
     * create a nested array of relationship paths one-level deep that can
     * be passed on to other serializers:
     *
     * ['user' => ['employer', 'employer.country'], 'comments' => []]
     *
     * @param array $paths
     *
     * @return array
     */
    public static function parseRelationshipPaths(array $paths): array
    {
        $tree = [];

        foreach ($paths as $path) {
            list($primary, $nested) = array_pad(explode('.', $path, 2), 2, null);

            if (!isset($tree[$primary])) {
                $tree[$primary] = [];
            }

            if ($nested) {
                $tree[$primary][] = $nested;
            }
        }

        return $tree;
    }
}
