<?php
declare(strict_types=1);

namespace JsonApi\Tests;

use JsonApi\Util;

class UtilTest extends AbstractTestCase
{
    public function testParseRelationshipPaths()
    {
        $this->assertEquals(
            ['user' => ['employer', 'employer.country'], 'comments' => []],
            Util::parseRelationshipPaths(['user', 'user.employer', 'user.employer.country', 'comments'])
        );

        $this->assertEquals(
            ['user' => ['employer.country']],
            Util::parseRelationshipPaths(['user.employer.country'])
        );
    }
}
