<?php
declare(strict_types=1);

namespace JsonApi\Tests;

use JsonApi\ErrorHandler;

class ErrorHandlerTest extends AbstractTestCase
{
    public function test_it_should_throw_an_exception_when_no_handlers_are_present()
    {
        $this->expectException(\RuntimeException::class);

        $handler = new ErrorHandler;

        $handler->handle(new \Exception);
    }
}
