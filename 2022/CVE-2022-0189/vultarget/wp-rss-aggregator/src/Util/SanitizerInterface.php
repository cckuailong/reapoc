<?php

namespace RebelCode\Wpra\Core\Util;

/**
 * Interface for objects that can sanitize values.
 *
 * @since 4.16
 */
interface SanitizerInterface
{
    /**
     * Sanitizes a given value.
     *
     * @since 4.16
     *
     * @param mixed $value The value to sanitize.
     *
     * @return mixed The sanitized value.
     */
    public function sanitize($value);
}
