<?php

namespace RebelCode\Entities\Schemas;

use RebelCode\Entities\Api\PropertyInterface;
use RebelCode\Entities\Api\SchemaInterface;

/**
 * A simple implementation for a generic schema.
 *
 * @since [*next-version*]
 */
class Schema implements SchemaInterface
{
    /**
     * @since [*next-version*]
     *
     * @var PropertyInterface[]
     */
    protected $properties;

    /**
     * @since [*next-version*]
     *
     * @var array
     */
    protected $defaults;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param PropertyInterface[] $properties A map of property keys to property instances.
     * @param array               $defaults   A map of property keys to their default values.
     */
    public function __construct(array $properties, array $defaults)
    {
        $this->properties = $properties;
        $this->defaults = $defaults;
    }

    /**
     * @inheritdoc
     *
     * @since [*next-version*]
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @inheritdoc
     *
     * @since [*next-version*]
     */
    public function getDefaults()
    {
        return $this->defaults;
    }
}
