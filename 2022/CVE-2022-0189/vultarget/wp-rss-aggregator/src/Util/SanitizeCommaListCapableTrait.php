<?php

namespace RebelCode\Wpra\Core\Util;

/**
 * Functionality for sanitizing a comma-separated string list into an array.
 *
 * @since 4.13
 */
trait SanitizeCommaListCapableTrait
{
    /**
     * Sanitizes a list of strings.
     *
     * @since 4.17
     *
     * @param string|array $value A comma separated string list or an array.
     *
     * @return array The list of strings.
     */
    protected function sanitizeCommaList($value)
    {
        if (empty($value)) {
            return [];
        }

        $array = is_array($value)
            ? $value
            : explode(',', strval($value));

        $ids = array_map(function ($part) {
            return trim($part);
        }, $array);

        return array_filter($ids);
    }

    /**
     * Sanitizes a list of IDs.
     *
     * @since 4.13
     *
     * @param string|array $value A comma separated string list or an array.
     *
     * @return array The list of IDs.
     */
    protected function sanitizeIdCommaList($value)
    {
        return array_map('intval', $this->sanitizeCommaList($value));
    }
}
