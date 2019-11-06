<?php
declare(strict_types=1);

namespace JsonApi\Tests\Exception\Handler;

use JsonApi\Exception\Handler\InvalidParameterExceptionHandler;
use JsonApi\Exception\Handler\ResponseBag;
use JsonApi\Exception\InvalidParameterException;
use PHPUnit\Framework\TestCase;

class InvalidParameterExceptionHandlerTest extends TestCase
{
    public function testHandlerCanManageInvalidParameterExceptions()
    {
        $handler = new InvalidParameterExceptionHandler();

        $this->assertTrue($handler->manages(new InvalidParameterException));
    }

    public function testHandlerCanNotManageOtherExceptions()
    {
        $handler = new InvalidParameterExceptionHandler();

        $this->assertFalse($handler->manages(new \Exception));
    }

    public function testErrorHandling()
    {
        $handler = new InvalidParameterExceptionHandler();
        $response = $handler->handle(new InvalidParameterException('error', 1, null, 'include'));

        $this->assertInstanceOf(ResponseBag::class, $response);
        $this->assertEquals(400, $response->getStatus());
        $this->assertEquals([['code' => 1, 'source' => ['parameter' => 'include']]], $response->getErrors());
    }
}
