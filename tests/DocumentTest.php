<?php
declare(strict_types=1);

namespace JsonApi\Tests;

use JsonApi\AbstractSerializer;
use JsonApi\Collection;
use JsonApi\Document;
use JsonApi\Relationship;
use JsonApi\Resource;

/**
 * This is the document test class.
 *
 * @author Toby Zerner <toby.zerner@gmail.com>
 */
class DocumentTest extends AbstractTestCase
{
    public function testToArrayIncludesTheResourcesRepresentation()
    {
        $post = (object) [
            'id'  => 1,
            'foo' => 'bar',
        ];

        $resource = new Resource($post, new PostSerializer2);

        $document = new Document($resource);

        $this->assertEquals(['data' => $resource->toArray()], $document->toArray());
    }

    public function testItCanBeSerializedToJson()
    {
        $this->assertEquals('[]', (string) new Document());
    }

    public function testToArrayIncludesIncludedResources()
    {
        $comment = (object) ['id' => 1, 'foo' => 'bar'];
        $post    = (object) ['id' => 1, 'foo' => 'bar', 'comments' => [$comment]];

        $resource         = new Resource($post, new PostSerializer2);
        $includedResource = new Resource($comment, new CommentSerializer2);

        $document = new Document($resource->with(['comments']));

        $this->assertEquals(
            [
                'data'     => $resource->toArray(),
                'included' => [
                    $includedResource->toArray(),
                ],
            ],
            $document->toArray()
        );
    }

    public function testNoEmptyAttributes()
    {
        $post = (object) [
            'id' => 1,
        ];

        $resource = new Resource($post, new PostSerializerEmptyAttributes2);

        $document = new Document($resource);

        $this->assertEquals('{"data":{"type":"posts","id":"1"}}', (string) $document, 'Attributes should be omitted');
    }
}

class PostSerializer2 extends AbstractSerializer
{
    public const TYPE = 'posts';

    public function getAttributes($post, array $fields = null): array
    {
        return ['foo' => $post->foo];
    }

    public function comments($post)
    {
        return new Relationship(new Collection($post->comments, new CommentSerializer2));
    }
}

class PostSerializerEmptyAttributes2 extends PostSerializer2
{
    public function getAttributes($post, array $fields = null): array
    {
        return [];
    }
}

class CommentSerializer2 extends AbstractSerializer
{
    public const TYPE = 'comments';

    public function getAttributes($comment, array $fields = null): array
    {
        return ['foo' => $comment->foo];
    }
}
