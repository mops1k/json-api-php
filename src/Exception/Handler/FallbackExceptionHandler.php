<?php
declare(strict_types=1);

namespace JsonApi\Exception\Handler;

class FallbackExceptionHandler implements ExceptionHandlerInterface
{
    /**
     * @var bool
     */
    private $debug;

    /**
     * @param bool $debug
     */
    public function __construct($debug)
    {
        $this->debug = $debug;
    }

    /**
     * {@inheritdoc}
     */
    public function manages(\Exception $e): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(\Exception $e): ResponseBag
    {
        $status = 500;
        $error = $this->constructError($e, $status);

        return new ResponseBag($status, [$error]);
    }

    /**
     * @param \Exception $e
     * @param $status
     *
     * @return array
     */
    private function constructError(\Exception $e, $status)
    {
        $error = ['code' => $status, 'title' => 'Internal server error'];

        if ($this->debug) {
            $error['detail'] = (string)$e;
        }

        return $error;
    }
}
