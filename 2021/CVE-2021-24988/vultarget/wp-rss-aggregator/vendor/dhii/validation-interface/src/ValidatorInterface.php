<?php

namespace Dhii\Validation;

use Dhii\Validation\Exception\ValidationExceptionInterface;
use Dhii\Validation\Exception\ValidationFailedExceptionInterface;

/**
 * Something that can validate a value.
 *
 * @since 0.1
 */
interface ValidatorInterface
{
    /**
     * Validates a value.
     *
     * @since 0.1
     *
     * @param mixed $value The subject of validation.
     *
     * @throws ValidationExceptionInterface       If problem validating.
     * @throws ValidationFailedExceptionInterface If validation failed.
     */
    public function validate($value);
}
