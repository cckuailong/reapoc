<?php

namespace Dhii\Validation;

/**
 * Something that can have a validator retrieved from it.
 *
 * @since 0.2
 */
interface ValidatorAwareInterface
{
    /**
     * Retrieves the validator.
     *
     * @since 0.2
     *
     * @return ValidatorInterface|null The validator, if any.
     */
    public function getValidator();
}
