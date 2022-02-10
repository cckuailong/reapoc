<?php

namespace RebelCode\Entities\Api;

/**
 * Interface for an entity schema, which describes the data configuration for entities.
 *
 * @since [*next-version*]
 */
interface SchemaInterface
{
    /**
     * @since [*next-version*]
     *
     * @return PropertyInterface[]
     */
    public function getProperties();

    /**
     * @since [*next-version*]
     *
     * @return array
     */
    public function getDefaults();
}
