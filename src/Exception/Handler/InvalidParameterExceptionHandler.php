<?php
declare(strict_types=1);

namespace JsonApi\Exception\Handler;

use JsonApi\Exception\InvalidParameterException;

class InvalidParameterExceptionHandler implements ExceptionHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function manages(\Exception $e): bool
    {
        return $e instanceof InvalidParameterException;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(\Exception $e): ResponseBag
    {
        $status = 400;
        $error = [];

        $code = $e->getCode();
        if ($code) {
            $error['code'] = $code;
        }

        $invalidParameter = $e->getInvalidParameter();
        if ($invalidParameter) {
            $error['source'] = ['parameter' => $invalidParameter];
        }

        return new ResponseBag($status, [$error]);
    }
}
