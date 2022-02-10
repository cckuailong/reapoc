<?php

namespace RebelCode\Wpra\Core\Util;

use InvalidArgumentException;

/**
 * Functionality for parsing array arguments against a known schema.
 *
 * @since 4.13
 */
trait ParseArgsWithSchemaCapableTrait
{
    /**
     * Parses an args array with a given schema.
     *
     * The schema is an array with each element's key being the arg key and each value being a sub-array, containing
     * the following data:
     *
     * - "default": Optional default value to use if the value is not in the args. If no default value is given, the
     *              entry is omitted from the result.
     * - "key":     Optional destination key to remap the args entry. The key may have delimiters (specified by the
     *              $delim argument) to set values deeper within the resulting array.
     * - "filter":  Optional {@link filter_var} filter to use. A custom "enum" filter is also available for filtering
     *              values to a restricted set of known presets defined in the "options" key. Alternatively, the
     *              filter may be a callable that accepts 3 arguments (the value, the full args array and the schema
     *              sub-array) and returns the filtered value or throws an {@link \InvalidArgumentException} to use
     *              the "default" value (or omit from the result if no default is given).
     * - "options": Optional filter options to use with the specified filter, or enum values if the filter is "enum".
     * - "flags":   Optional filter flags to use with the specified filter.
     *
     * @since 4.13
     *
     * @param array       $args   The args to parse.
     * @param array       $schema The schema.
     * @param string      $delim  Optional string delimiter to split keys into paths.
     * @param string|null $else   Optional key where non-schema data will be stored, or null to omit non-schema data.
     *
     * @return array The parsed arguments arrays.
     */
    protected function parseArgsWithSchema($args, $schema, $delim = '/', $else = null)
    {
        $prepared = [];

        foreach ($schema as $_key => $_singleSchema) {
            // Check if the args has the value
            $hasValue = array_key_exists($_key, $args);
            $hasDefault = array_key_exists('default', $_singleSchema);

            // If no value and no default, skip this entry
            if (!$hasValue && !$hasDefault) {
                continue;
            }

            // Get the value, using the default if missing
            $origValue = ($hasValue)
                ? $args[$_key]
                : $_singleSchema['default'];

            // Get the filter and its options, if provided
            $filter = array_key_exists('filter', $_singleSchema)
                ? $_singleSchema['filter']
                : null;
            $filterOpts = array_key_exists('options', $_singleSchema)
                ? $_singleSchema['options']
                : [];
            $filterFlags = array_key_exists('flags', $_singleSchema)
                ? $_singleSchema['flags']
                : [];

            $callback = null;
            if (is_callable($filter)) {
                $callback = $filter;
                $filter = null;
            }

            // Custom enum filter becomes the callback
            if ($filter === "enum") {
                $callback = function ($value) use ($filterOpts) {
                    if (!in_array($value, $filterOpts)) {
                        throw new InvalidArgumentException();
                    }

                    return $value;
                };
                $filter = null;
            }

            // Filter the value
            $value = ($filter !== null)
                ? filter_var($origValue, $filter, ['flags' => $filterFlags, 'options' => $filterOpts])
                : $origValue;

            // If the value is NOT the default and a sanitize function is set in the schema,
            // Run the value through the sanitization callback function
            try {
                $finalValue = ($hasValue && $callback !== null)
                    ? call_user_func_array($callback, [$value, $args, $_singleSchema])
                    : $value;
            } catch (InvalidArgumentException $exception) {
                continue;
            }

            // Get the destination key from schema if given, using the original key if not
            $destKey = array_key_exists('key', $_singleSchema)
                ? $_singleSchema['key']
                : $_key;

            // Explode the key into an array path using the param delimiter
            $pathKey = explode($delim, $destKey);

            $this->_arrayDeepSet($prepared, $pathKey, $finalValue);
        }

        if (is_string($else) && strlen($else) > 0) {
            $prepared[$else] = array_diff_key($args, $schema);
        }

        return $prepared;
    }

    /**
     * Utility method for setting a deep value in an array.
     *
     * @since 4.13
     *
     * @param array $array The array in which to set the value.
     * @param array $path  An array of keys, each corresponding to a path segment.
     * @param mixed $value The value to set.
     */
    protected function _arrayDeepSet(&$array, $path, $value)
    {
        if (empty($path)) {
            return;
        }

        $head = array_shift($path);

        if (count($path) === 0) {
            $array[$head] = $value;

            return;
        }

        if (!array_key_exists($head, $array)) {
            $array[$head] = [];
        }

        static::_arrayDeepSet($array[$head], $path, $value);
    }
}
