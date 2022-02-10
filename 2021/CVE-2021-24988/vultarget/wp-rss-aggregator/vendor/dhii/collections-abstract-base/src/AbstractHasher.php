<?php

namespace Dhii\Collection;

/**
 * Generic functionality for hashing.
 *
 * @since 0.1.0
 */
class AbstractHasher
{
    /**
     * Creates a hash of any value.
     *
     * @since 0.1.0
     * @see _hashScalar()
     * @see _hashNonScalar()
     *
     * @param mixed $value Any value.
     *
     * @return The value's hash.
     */
    protected function _hash($value)
    {
        return is_scalar($value)
                ? $this->_hashScalar($value)
                : $this->_hashNonScalar($value);
    }

    /**
     * Creates a hash of a scalar value.
     *
     * @since 0.1.0
     * @see sha1()
     *
     * @param int|float|string $value The scalar value to hash.
     *
     * @return string The value's {@link sha1() SHA1} hash.
     */
    protected function _hashScalar($value)
    {
        return sha1($value);
    }

    /**
     * Creates a hash of a non-scalar value.
     *
     * @since 0.1.0
     *
     * @param object|array $value The non-scalar value to hash.
     *
     * @return string The value's hash. Always same for same object, but
     *                different for different identical objects. Same for identical arrays,
     *                but different for arrays with same data in a different order.
     */
    protected function _hashNonScalar($value)
    {
        return is_object($value)
                ? $this->_hashScalar(spl_object_hash($value))
                : $this->_hashScalar(serialize($value));
    }
}
