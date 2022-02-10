<?php

namespace RebelCode\Entities\Properties;

use RebelCode\Entities\Api\EntityInterface;
use RebelCode\Entities\Api\PropertyInterface;

/**
 * A simple implementation of a property that just reads and writes values to/from the data store.
 *
 * @since [*next-version*]
 */
class Property implements PropertyInterface
{
    /**
     * @since [*next-version*]
     *
     * @var string
     */
    protected $key;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param string $key The data store key to read from and write to.
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * @inheritdoc
     *
     * @since [*next-version*]
     */
    public function getValue(EntityInterface $entity)
    {
        return $entity->getStore()->get($this->key);
    }

    /**
     * @inheritdoc
     *
     * @since [*next-version*]
     */
    public function setValue(EntityInterface $entity, $value)
    {
        return [$this->key => $value];
    }
}
