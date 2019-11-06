<?php
declare(strict_types=1);

namespace JsonApi\Tests;

use JsonApi\Exception\InvalidParameterException;
use JsonApi\Parameters;

/**
 * This is the parameters test class.
 *
 * @author Toby Zerner <toby.zerner@gmail.com>
 */
class ParametersTest extends AbstractTestCase
{
    /**
     * @throws InvalidParameterException
     */
    public function testGetIncludeReturnsArrayOfIncludes()
    {
        $parameters = new Parameters(['include' => 'posts,images']);

        $this->assertEquals(['posts', 'images'], $parameters->getInclude(['posts', 'images']));
    }

    /**
     * @throws InvalidParameterException
     */
    public function testGetIncludeReturnsEmptyArray()
    {
        $parameters = new Parameters(['include' => '']);

        $this->assertEquals([], $parameters->getInclude(['posts', 'images']));
    }

    /**
     * @throws InvalidParameterException
     */
    public function testGetIncludeWithUnallowedField()
    {
        $this->expectException(InvalidParameterException::class);
        $this->expectExceptionCode(1);
        $parameters = new Parameters(['include' => 'posts,images']);

        $parameters->getInclude(['posts']);
    }

    /**
     * @throws InvalidParameterException
     */
    public function testGetSortReturnsArrayOfFieldToSortDirection()
    {
        $parameters = new Parameters(['sort' => 'firstname']);

        $this->assertEquals(['firstname' => 'asc'], $parameters->getSort(['firstname']));
    }

    /**
     * @throws InvalidParameterException
     */
    public function testGetSortSupportsMultipleSortedFieldsSeparatedByComma()
    {
        $parameters = new Parameters(['sort' => 'firstname,-lastname']);

        $this->assertEquals(['firstname' => 'asc', 'lastname' => 'desc'], $parameters->getSort(['firstname', 'lastname']));
    }

    /**
     * @throws InvalidParameterException
     */
    public function testGetSortDefaultsToEmptyArray()
    {
        $parameters = new Parameters([]);

        $this->assertEmpty($parameters->getSort());
    }

    /**
     * @throws InvalidParameterException
     */
    public function testGetSortWithUnallowedField()
    {
        $this->expectException(InvalidParameterException::class);
        $this->expectExceptionCode(3);
        $parameters = new Parameters(['sort' => 'firstname,lastname']);

        $parameters->getSort(['firstname']);
    }

    /**
     * @throws InvalidParameterException
     */
    public function testGetOffsetParsesThePageOffset()
    {
        $parameters = new Parameters(['page' => ['offset' => 10]]);

        $this->assertEquals(10, $parameters->getOffset());
    }

    /**
     * @throws InvalidParameterException
     */
    public function testGetOffsetIsAtLeastZero()
    {
        $this->expectException(InvalidParameterException::class);
        $this->expectExceptionCode(2);
        $parameters = new Parameters(['page' => ['offset' => -5]]);

        $parameters->getOffset();
    }

    /**
     * @throws InvalidParameterException
     */
    public function testGetOffsetParsesThePageNumber()
    {
        $parameters = new Parameters(['page' => ['number' => 2]]);

        $this->assertEquals(20, $parameters->getOffset(20));
    }

    /**
     * testGetLimitParsesThePageLimit
     */
    public function testGetLimitParsesThePageLimit()
    {
        $parameters = new Parameters(['page' => ['limit' => 100]]);

        $this->assertEquals(100, $parameters->getLimit());
    }

    /**
     * testGetLimitReturnsNullWhenNotSet
     */
    public function testGetLimitReturnsNullWhenNotSet()
    {
        $parameters = new Parameters(['page' => ['offset' => 50]]);

        $this->assertNull($parameters->getLimit());
    }

    /**
     * testGetLimitReturnsNullWhenNotSet
     */
    public function testGetFieldsReturnsAllFields()
    {
        $parameters = new Parameters(['fields' => ['posts' => 'title,content', 'users' => 'name']]);

        $this->assertEquals(['posts' => ['title', 'content'], 'users' => ['name']], $parameters->getFields());
    }

    /**
     * testGetLimitReturnsNullWhenNotSet
     */
    public function testGetFieldsReturnsEmptyArray()
    {
        $parameters = new Parameters([]);

        $this->assertEquals([], $parameters->getFields());

        $parameters = new Parameters(['fields' => 'string']);

        $this->assertEquals([], $parameters->getFields());
    }
}
