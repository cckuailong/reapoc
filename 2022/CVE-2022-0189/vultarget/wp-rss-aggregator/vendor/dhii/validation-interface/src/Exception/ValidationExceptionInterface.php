<?php

namespace Dhii\Validation\Exception;

use Dhii\Exception\ThrowableInterface;
use Dhii\Validation\ValidatorAwareInterface;

/**
 * Something that can represent an exception which can occur in or be related to
 * a validation process or component.
 *
 * @since 0.1
 */
interface ValidationExceptionInterface extends
        ThrowableInterface,
        ValidatorAwareInterface
{
}
