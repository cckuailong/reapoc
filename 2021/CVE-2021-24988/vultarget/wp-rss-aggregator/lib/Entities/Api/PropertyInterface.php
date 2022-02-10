<?php

namespace RebelCode\Entities\Api;

use OutOfBoundsException;

/**
 * The interface for entity properties.
 *
 * A property is an object that is responsible for reading and writing data from and to a store, as well as performing
 * any necessary sanitization or parsing before or after any read or write operations.
 *
 * @since [*next-version*]
 */
interface PropertyInterface
{
    /**
     * @since [*next-version*]
     *
     * @param EntityInterface $entity
     *
     * @return mixed
     *
     * @throws OutOfBoundsException
     */
    public function getValue(EntityInterface $entity);

    /**
     * @since [*next-version*]
     *
     * @param EntityInterface $entity
     * @param mixed           $value
     *
     * @return array The commit to be given to a data store.
     *
     * @throws OutOfBoundsException
     */
    public function setValue(EntityInterface $entity, $value);
}
