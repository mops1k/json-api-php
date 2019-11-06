<?php
declare(strict_types=1);

namespace JsonApi\Tests;

use JsonApi\AbstractSerializer;
use JsonApi\Collection;
use JsonApi\Resource;

/**
 * This is the collection test class.
 *
 * @author Toby Zerner <toby.zerner@gmail.com>
 */
class CollectionTest extends AbstractTestCase
{
    public function testToArrayReturnsArrayOfResources()
    {
        $serializer = new PostSerializer3;

        $post1 = (object)['id' => 1, 'foo' => 'bar'];
        $post2 = new Resource((object)['id' => 2, 'foo' => 'baz'], $serializer);

        $collection = new Collection([$post1, $post2], $serializer);

        $resource1 = new Resource($post1, $serializer);
        $resource2 = $post2;

        $this->assertEquals([$resource1->toArray(), $resource2->toArray()], $collection->toArray());
    }

    public function testToIdentifierReturnsArrayOfResourceIdentifiers()
    {
        $serializer = new PostSerializer3;

        $post1 = (object)['id' => 1];
        $post2 = (object)['id' => 2];

        $collection = new Collection([$post1, $post2], $serializer);

        $resource1 = new Resource($post1, $serializer);
        $resource2 = new Resource($post2, $serializer);

        $this->assertEquals([$resource1->toIdentifier(), $resource2->toIdentifier()], $collection->toIdentifier());
    }
}

class PostSerializer3 extends AbstractSerializer
{
    public const TYPE = 'posts';

    public function getAttributes($post, array $fields = null): array
    {
        return ['foo' => $post->foo];
    }
}
