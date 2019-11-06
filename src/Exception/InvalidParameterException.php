<?php
declare(strict_types=1);

namespace JsonApi\Exception;

class InvalidParameterException extends \Exception
{
    /**
     * @var string The parameter that caused this exception.
     */
    private $invalidParameter;

    /**
     * {@inheritdoc}
     *
     * @param string $invalidParameter The parameter that caused this exception.
     */
    public function __construct($message = '', $code = 0, $previous = null, $invalidParameter = '')
    {
        parent::__construct($message, $code, $previous);

        $this->invalidParameter = $invalidParameter;
    }

    /**
     * @return string The parameter that caused this exception.
     */
    public function getInvalidParameter()
    {
        return $this->invalidParameter;
    }
}
