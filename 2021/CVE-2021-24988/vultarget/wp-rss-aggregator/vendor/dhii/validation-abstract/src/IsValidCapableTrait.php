<?php

namespace Dhii\Validation;

use Dhii\Validation\Exception\ValidationFailedExceptionInterface;
use Exception as RootException;

/**
 * Functionality for determining if something is valid.
 *
 * @since [*next-version*]
 */
trait IsValidCapableTrait
{
    /**
     * Determines whether the subject is valid.
     *
     * @since [*next-version*]
     *
     * @param mixed $subject The value to validate.
     *
     * @throws RootException If problem validating.
     *
     * @return bool True if the subject is valid; false otherwise.
     */
    protected function _isValid($subject)
    {
        try {
            $this->_validate($subject);
        } catch (ValidationFailedExceptionInterface $e) {
            return false;
        }

        return true;
    }

    /**
     * Validates a subject.
     *
     * @since [*next-version*]
     *
     * @param mixed $subject The value to validate.
     *
     * @throws ValidationFailedExceptionInterface If subject is invalid.
     * @throws RootException                      If problem validating.
     */
    abstract protected function _validate($subject);
}
