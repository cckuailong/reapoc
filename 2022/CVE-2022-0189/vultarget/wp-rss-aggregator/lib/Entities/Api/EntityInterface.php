<?php

namespace RebelCode\Entities\Api;

use OutOfBoundsException;

/**
 * Represents a data entity.
 *
 * @since [*next-version*]
 */
interface EntityInterface
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
     * @param array $data
     *
     * @return EntityInterface
     *
     * @throws OutOfBoundsException
     */
    public function set(array $data);

    /**
     * @since [*next-version*]
     *
     * @return StoreInterface
     */
    public function getStore();

    /**
     * @since [*next-version*]
     *
     * @return SchemaInterface
     */
    public function getSchema();

    /**
     * @since [*next-version*]
     *
     * @return array
     */
    public function export();
}
