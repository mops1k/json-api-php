<?php
declare(strict_types=1);

namespace JsonApi\Tests\Exception\Handler;

use JsonApi\Exception\Handler\FallbackExceptionHandler;
use JsonApi\Exception\Handler\ResponseBag;

class FallbackExceptionHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testHandlerCanManageExceptions()
    {
        $handler = new FallbackExceptionHandler(false);

        $this->assertTrue($handler->manages(new \Exception));
    }

    public function testErrorHandlingWithoutDebugMode()
    {
        $handler = new FallbackExceptionHandler(false);
        $response = $handler->handle(new \Exception);

        $this->assertInstanceOf(ResponseBag::class, $response);
        $this->assertEquals(500, $response->getStatus());
        $this->assertEquals([['code' => 500, 'title' => 'Internal server error']], $response->getErrors());
    }

    public function testErrorHandlingWithDebugMode()
    {
        $handler = new FallbackExceptionHandler(true);
        $response = $handler->handle(new \Exception);

        $this->assertInstanceOf(ResponseBag::class, $response);
        $this->assertEquals(500, $response->getStatus());
        $this->assertArrayHasKey('detail', $response->getErrors()[0]);
    }
}
