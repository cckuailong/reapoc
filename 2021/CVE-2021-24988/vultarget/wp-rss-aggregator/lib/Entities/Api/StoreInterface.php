<?php

namespace RebelCode\Entities\Api;

use OutOfBoundsException;

/**
 * Interface for a data store, which is a simple abstraction for the storage of raw entity data.
 *
 * @since [*next-version*]
 */
interface StoreInterface
{
    /**
     * @since [*next-version*]
     *
     * @param string $key
     *
     * @return mixed
     *
     * @throws OutOfBoundsException
     */
    public function get($key);

    /**
     * @since [*next-version*]
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key);

    /**
     * @since [*next-version*]
     *
     * @param array $data
     *
     * @return StoreInterface
     *
     * @throws OutOfBoundsException
     */
    public function set(array $data);
}
