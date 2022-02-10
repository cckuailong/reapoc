<?php

namespace Dhii\Validation\Exception;

use Dhii\Exception\AbstractBaseException;
use Dhii\Validation\ValidatorAwareTrait;

/**
 * Common functionality for validation exception.
 *
 * @since [*next-version*]
 */
class AbstractBaseValidationException extends AbstractBaseException implements ValidationExceptionInterface
{
    /*
     * Adds validator awareness.
     *
     * @since [*next-version*]
     */
    use ValidatorAwareTrait;

    /**
     * Parameter-less constructor.
     *
     * Invoke this in actual constructor.
     *
     * @since [*next-version*]
     */
    protected function _construct()
    {
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function getValidator()
    {
        return $this->_getValidator();
    }
}
