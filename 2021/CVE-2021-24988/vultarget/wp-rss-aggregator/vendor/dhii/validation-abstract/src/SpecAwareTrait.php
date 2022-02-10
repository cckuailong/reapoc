<?php

namespace Dhii\Validation;

use stdClass;
use Traversable;
use InvalidArgumentException;

/**
 * Functionality for validation spec awareness.
 *
 * @since [*next-version*]
 */
trait SpecAwareTrait
{
    /**
     * The spec.
     *
     * @since [*next-version*]
     *
     * @var array|Traversable|stdClass|null
     */
    protected $spec;

    /**
     * Retrieves the spec associated with this instance.
     *
     * @since [*next-version*]
     *
     * @return array|Traversable|stdClass|null The spec.
     */
    protected function _getSpec()
    {
        return $this->spec;
    }

    /**
     * Assigns a spec to this instance.
     *
     * @since [*next-version*]
     *
     * @param array|Traversable|stdClass|null $spec The spec.
     */
    protected function _setSpec($spec)
    {
        if (!is_null($spec)) {
            $spec = $this->_normalizeIterable($spec);
        }

        $this->spec = $spec;
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
