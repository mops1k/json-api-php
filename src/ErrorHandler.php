<?php
declare(strict_types=1);

namespace JsonApi;

use JsonApi\Exception\Handler\ExceptionHandlerInterface;
use JsonApi\Exception\Handler\ResponseBag;

class ErrorHandler
{
    /**
     * Stores the valid handlers.
     *
     * @var ExceptionHandlerInterface[]
     */
    private $handlers = [];

    /**
     * Handle the exception provided.
     *
     * @param \Exception $e
     *
     * @return ResponseBag
     * @throws \RuntimeException
     */
    public function handle(\Exception $e): ResponseBag
    {
        foreach ($this->handlers as $handler) {
            if ($handler->manages($e)) {
                return $handler->handle($e);
            }
        }

        throw new \RuntimeException(
            \sprintf('Exception handler for %s not found.', \get_class($e))
        );
    }

    /**
     * Register a new exception handler.
     *
     * @param ExceptionHandlerInterface $handler
     *
     * @return void
     */
    public function registerHandler(ExceptionHandlerInterface $handler): void
    {
        $this->handlers[] = $handler;
    }
}
