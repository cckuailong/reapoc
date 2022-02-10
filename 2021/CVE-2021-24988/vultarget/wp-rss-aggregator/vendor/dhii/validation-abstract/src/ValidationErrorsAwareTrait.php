<?php

namespace Dhii\Validation;

use stdClass;
use Traversable;
use InvalidArgumentException;

/**
 * Functionality for retrieving the subject.
 *
 * @since [*next-version*]
 */
trait ValidationErrorsAwareTrait
{
    /**
     * The list of validation errors associated with this instance.
     *
     * @since [*next-version*]
     *
     * @var array|Traversable|stdClass|null
     */
    protected $validationErrors;

    /**
     * Retrieve the list of validation errors that this instance represents.
     *
     * @since [*next-version*]
     *
     * @return array|Traversable|stdClass The error list.
     */
    protected function _getValidationErrors()
    {
        if (is_null($this->validationErrors)) {
            return array();
        }

        return $this->validationErrors;
    }

    /**
     * Sets the list of validation errors that this instance should represent.
     *
     * @since [*next-version*]
     *
     * @param array|Traversable|stdClass|null $errorList The list of errors.
     */
    protected function _setValidationErrors($errorList)
    {
        if (!is_null($errorList)) {
            $errorList = $this->_normalizeIterable($errorList);
        }

        $this->validationErrors = $errorList;
    }

    /**
     * Normalizes an iterable.
     *
     * Makes sure that the return value can be iterated over.
     *
     * @since [*next-version*]
     *
     * @param mixed $iterable The iterable to normalize.
     *
     * @throws InvalidArgumentException If the iterable could not be normalized.
     *
     * @return array|Traversable|stdClass The normalized iterable.
     */
    abstract protected function _normalizeIterable($iterable);
}
