<?php

namespace Dhii\Validation;

use stdClass;
use Traversable;
use InvalidArgumentException;

/**
 * Awareness of child validators.
 *
 * @since [*next-version*]
 */
trait ChildValidatorsAwareTrait
{
    /**
     * The child validators.
     *
     * @since [*next-version*]
     *
     * @var array|Traversable|stdClass
     */
    protected $childValidators;

    /**
     * Retrieves the list of child validators associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return array|Traversable|stdClass The list of validators.
     */
    protected function _getChildValidators()
    {
        if (is_null($this->childValidators)) {
            return array();
        }

        return $this->childValidators;
    }

    /**
     * Retrieves the child validators.
     *
     * @since [*next-version*]
     *
     * @param ValidatorInterface[]|Traversable|stdClass $validators A list of validators.
     *
     * @throws InvalidArgumentException If the validators list is invalid.
     */
    protected function _setChildValidators($validators)
    {
        $validators = $this->_normalizeIterable($validators);

        $this->childValidators = $validators;
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

    /**
     * Translates a string, and replaces placeholders.
     *
     * @since [*next-version*]
     * @see sprintf()
     *
     * @param string $string  The format string to translate.
     * @param array  $args    Placeholder values to replace in the string.
     * @param mixed  $context The context for translation.
     *
     * @return string The translated string.
     */
    abstract protected function __($string, $args = array(), $context = null);
}
