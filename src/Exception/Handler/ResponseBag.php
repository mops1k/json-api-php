<?php
declare(strict_types=1);

namespace JsonApi\Exception\Handler;

/**
 * DTO to manage JSON error response handling.
 */
class ResponseBag
{
    /** @var int */
    private $status;

    /** @var array */
    private $errors;

    /**
     * @param int $status
     * @param array $errors
     */
    public function __construct($status, array $errors)
    {
        $this->status = $status;
        $this->errors = $errors;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }
}
