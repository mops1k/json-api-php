<?php
declare(strict_types=1);

namespace JsonApi\Tests;

use JsonApi\AbstractSerializer;
use JsonApi\Collection;
use JsonApi\Relationship;
use JsonApi\Resource;

class AbstractSerializerTest extends AbstractTestCase
{
    public function testGetTypeReturnsTheType()
    {
        $serializer = new PostSerializer1;

        $this->assertEquals('posts', $serializer->getType(null));
    }

    public function testGetAttributesReturnsTheAttributes()
    {
        $serializer = new PostSerializer1;
        $post = (object)['foo' => 'bar'];

        $this->assertEquals(['foo' => 'bar'], $serializer->getAttributes($post));
    }

    public function testGetRelationshipReturnsRelationshipFromMethod()
    {
        $serializer = new PostSerializer1;

        $relationship = $serializer->getRelationship(null, 'comments');

        $this->assertTrue($relationship instanceof Relationship);
    }

    public function testGetRelationshipReturnsRelationshipFromMethodUnderscored()
    {
        $serializer = new PostSerializer1;

        $relationship = $serializer->getRelationship(null, 'parent_post');

        $this->assertInstanceOf(Relationship::class, $relationship);
    }

    public function testGetRelationshipReturnsRelationshipFromMethodKebabCase()
    {
        $serializer = new PostSerializer1;

        $relationship = $serializer->getRelationship(null, 'parent-post');

        $this->assertInstanceOf(Relationship::class, $relationship);
    }

    public function testGetRelationshipValidatesRelationship()
    {
        $this->expectException(\LogicException::class);
        $serializer = new PostSerializer1;

        $serializer->getRelationship(null, 'invalid');
    }

    /**
     * @throws \LogicException
     */
    public function testEmptyType()
    {
        $this->expectException(\LogicException::class);
        $serializer = new TypeSerializer();

        $serializer->getType(null);
    }
}

class PostSerializer1 extends AbstractSerializer
{
    public const TYPE = 'posts';

    public function getAttributes($post, array $fields = null): array
    {
        return ['foo' => $post->foo];
    }

    public function comments($post)
    {
        $element = new Collection([], new self);

        return new Relationship($element);
    }

    public function parentPost($post)
    {
        $element = new Resource([], new self);

        return new Relationship($element);
    }

    public function invalid($post)
    {
        return 'invalid';
    }
}

class TypeSerializer extends AbstractSerializer
{
    // @stub
}
