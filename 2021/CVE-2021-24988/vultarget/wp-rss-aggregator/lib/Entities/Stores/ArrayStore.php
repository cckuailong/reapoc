<?php

namespace RebelCode\Entities\Stores;

use OutOfBoundsException;
use RebelCode\Entities\Api\StoreInterface;

/**
 * A simple implementation of a data store that keeps the data in an internal array.
 *
 * @since [*next-version*]
 */
class ArrayStore implements StoreInterface
{
    /**
     * @since [*next-version*]
     *
     * @var array
     */
    protected $data;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @inheritdoc
     *
     * @since [*next-version*]
     */
    public function get($key)
    {
        if (!array_key_exists($key, $this->data)) {
            throw new OutOfBoundsException();
        }

        return $this->data[$key];
    }

    /**
     * @inheritdoc
     *
     * @since [*next-version*]
     */
    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * @inheritdoc
     *
     * @since [*next-version*]
     */
    public function set(array $data)
    {
        $instance = clone $this;

        foreach ($data as $key => $value) {
            $instance->data[$key] = $value;
        }

        return $instance;
    }
}
