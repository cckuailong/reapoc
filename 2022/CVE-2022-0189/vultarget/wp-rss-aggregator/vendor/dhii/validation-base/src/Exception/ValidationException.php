<?php

namespace Dhii\Validation\Exception;

use Exception as RootException;
use Dhii\Validation\ValidatorInterface;
use Dhii\Util\String\StringableInterface as Stringable;

/**
 * Represents an exception which occurs inside of or related to a validation
 * process.
 *
 * @since 0.1
 */
class ValidationException extends AbstractBaseValidationException implements ValidationExceptionInterface
{
    /**
     * @since [*next-version*]
     *
     * @param string|Stringable|null  $message   The error message, if any.
     * @param int|null                $code      The error code, if any.
     * @param RootException|null      $previous  The inner exception, if any.
     * @param ValidatorInterface|null $validator The validator, if any.
     */
    public function __construct($message = null, $code = null, RootException $previous = null, $validator = null)
    {
        $this->_initBaseException($message, $code, $previous);
        $this->_setValidator($validator);
        $this->_construct();
    }
}
