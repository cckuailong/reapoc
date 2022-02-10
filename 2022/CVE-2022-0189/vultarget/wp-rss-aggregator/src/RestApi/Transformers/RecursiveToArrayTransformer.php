<?php

namespace RebelCode\Wpra\Core\RestApi\Transformers;

use Dhii\Transformer\TransformerInterface;
use stdClass;
use Traversable;

/**
 * A transformer that recursively unpacks iterable values into arrays.
 *
 * @since 4.13
 */
class RecursiveToArrayTransformer implements TransformerInterface
{
    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function transform($source)
    {
        if (!$this->isIterable($source)) {
            return $source;
        }

        return $this->iterableToArray($source);
    }

    /**
     * Recursively unpacks an iterable value and all of its iterable children into arrays.
     *
     * @since 4.13
     *
     * @param array|stdClass|Traversable $input The iterable value to unpack.
     *
     * @return array The unpacked array.
     */
    protected function iterableToArray($input)
    {
        $output = [];

        foreach ($input as $key => $value) {
            $output[$key] = $this->isIterable($value)
                ? $this->iterableToArray($value)
                : $value;
        }

        return $output;
    }

    /**
     * Checks if a value is iterable.
     *
     * @since 4.13
     *
     * @param mixed $value The value to check.
     *
     * @return bool True if the value is iterable, false if not.
     */
    protected function isIterable($value)
    {
        return is_array($value) || $value instanceof stdClass || $value instanceof Traversable;
    }
}
