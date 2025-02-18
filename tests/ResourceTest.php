<?php
declare(strict_types=1);

namespace JsonApi\Tests;

use JsonApi\AbstractSerializer;
use JsonApi\Collection;
use JsonApi\Relationship;
use JsonApi\Resource;

class ResourceTest extends AbstractTestCase
{
    public function testToArrayReturnsArray()
    {
        $data = (object)['id' => '123', 'foo' => 'bar', 'baz' => 'qux'];

        $resource = new Resource($data, new PostSerializer4WithLinksAndMeta);

        $this->assertEquals([
            'type' => 'posts',
            'id' => '123',
            'attributes' => [
                'foo' => 'bar',
                'baz' => 'qux'
            ],
            'links' => [
                'self' => '/posts/123'
            ],
            'meta' => [
                'some-meta' => 'from-serializer-for-123'
            ]
        ], $resource->toArray());
    }

    public function testToIdentifierReturnsResourceIdentifier()
    {
        $data = (object)['id' => '123', 'foo' => 'bar'];

        $resource = new Resource($data, new PostSerializer4);

        $this->assertEquals([
            'type' => 'posts',
            'id' => '123'
        ], $resource->toIdentifier());

        $resource->addMeta('foo', 'bar');

        $this->assertEquals([
            'type' => 'posts',
            'id' => '123',
            'meta' => ['foo' => 'bar']
        ], $resource->toIdentifier());
    }

    public function testGetIdReturnsString()
    {
        $data = (object)['id' => 123];

        $resource = new Resource($data, new PostSerializer4);

        $this->assertSame('123', $resource->getId());
    }

    public function testGetIdWorksWithScalarData()
    {
        $resource = new Resource(123, new PostSerializer4);

        $this->assertSame('123', $resource->getId());
    }

    public function testCanFilterFields()
    {
        $data = (object)['id' => '123', 'foo' => 'bar', 'baz' => 'qux'];

        $resource = new Resource($data, new PostSerializer4);

        $resource->fields(['posts' => ['baz']]);

        $this->assertEquals([
            'type' => 'posts',
            'id' => '123',
            'attributes' => [
                'baz' => 'qux'
            ]
        ], $resource->toArray());
    }

    public function testCanMergeWithAnotherResource()
    {
        $post1 = (object)['id' => '123', 'foo' => 'bar', 'comments' => [1]];
        $post2 = (object)['id' => '123', 'baz' => 'qux', 'comments' => [1, 2]];

        $resource1 = new Resource($post1, new PostSerializer4);
        $resource2 = new Resource($post2, new PostSerializer4);

        $resource1->with(['comments']);
        $resource2->with(['comments']);

        $resource1->merge($resource2);

        $this->assertEquals([
            'type' => 'posts',
            'id' => '123',
            'attributes' => [
                'baz' => 'qux',
                'foo' => 'bar'
            ],
            'relationships' => [
                'comments' => [
                    'data' => [
                        ['type' => 'comments', 'id' => '1'],
                        ['type' => 'comments', 'id' => '2']
                    ]
                ]
            ]
        ], $resource1->toArray());
    }

    public function testLinksMergeWithSerializerLinks()
    {
        $post1 = (object)['id' => '123', 'foo' => 'bar', 'comments' => [1]];

        $resource1 = new Resource($post1, new PostSerializer4WithLinksAndMeta());
        $resource1->addLink('self', 'overridden/by/resource');
        $resource1->addLink('related', '/some/other/comment');

        $this->assertEquals([
            'type' => 'posts',
            'id' => '123',
            'attributes' => [
                'foo' => 'bar'
            ],
            'links' => [
                'self' => 'overridden/by/resource',
                'related' => '/some/other/comment'
            ],
            'meta' => [
                'some-meta' => 'from-serializer-for-123'
            ]
        ], $resource1->toArray());
    }

    public function testMetaMergeWithSerializerLinks()
    {
        $post1 = (object)['id' => '123', 'foo' => 'bar', 'comments' => [1]];

        $resource1 = new Resource($post1, new PostSerializer4WithLinksAndMeta());
        $resource1->addMeta('some-meta', 'overridden-by-resource');

        $this->assertEquals([
            'type' => 'posts',
            'id' => '123',
            'attributes' => [
                'foo' => 'bar'
            ],
            'links' => [
                'self' => '/posts/123'
            ],
            'meta' => [
                'some-meta' => 'overridden-by-resource'
            ]
        ], $resource1->toArray());
    }

    public function testEmptyToOneRelationships()
    {
        $post1 = (object)['id' => '123', 'foo' => 'bar'];

        $resource1 = new Resource($post1, new PostSerializer4());
        $resource1->with(['author']);

        $this->assertEquals([
            'type' => 'posts',
            'id' => '123',
            'attributes' => [
                'foo' => 'bar'
            ],
            'relationships' => [
                'author' => ['data' => null]
            ]
        ], $resource1->toArray());
    }

    public function testEmptyToManyRelationships()
    {
        $post1 = (object)['id' => '123', 'foo' => 'bar'];

        $resource1 = new Resource($post1, new PostSerializer4());
        $resource1->with(['likes']);

        $this->assertEquals([
            'type' => 'posts',
            'id' => '123',
            'attributes' => [
                'foo' => 'bar'
            ],
            'relationships' => [
                'likes' => ['data' => []]
            ]
        ], $resource1->toArray());
    }
}

class PostSerializer4 extends AbstractSerializer
{
    public const TYPE = 'posts';

    public function getAttributes($post, array $fields = null): array
    {
        $attributes = [];

        if (isset($post->foo)) {
            $attributes['foo'] = $post->foo;
        }
        if (isset($post->baz)) {
            $attributes['baz'] = $post->baz;
        }

        return $attributes;
    }

    public function comments($post)
    {
        return new Relationship(new Collection($post->comments, new CommentSerializer));
    }

    public function author($post)
    {
        return new Relationship(new Resource(null, new CommentSerializer));
    }

    public function likes($post)
    {
        return new Relationship(new Collection([], new CommentSerializer));
    }
}

class PostSerializer4WithLinksAndMeta extends PostSerializer4
{
    public function getLinks($post): array
    {
        return ['self' => sprintf('/posts/%s', $post->id)];
    }

    public function getMeta($post): array
    {
        return ['some-meta' => sprintf('from-serializer-for-%s', $post->id)];
    }
}

class CommentSerializer extends AbstractSerializer
{
    public const TYPE = 'comments';
}
