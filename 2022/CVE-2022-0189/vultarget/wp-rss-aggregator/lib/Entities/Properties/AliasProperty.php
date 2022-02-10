<?php

namespace RebelCode\Entities\Properties;

use OutOfBoundsException;
use RebelCode\Entities\Api\EntityInterface;
use RebelCode\Entities\Api\PropertyInterface;

/**
 * A property implementation that acts as an alias of another property.
 *
 * @since [*next-version*]
 */
class AliasProperty implements PropertyInterface
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
     * @param string $key The key of the original property to be aliased.
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
        return $entity->get($this->key);
    }

    /**
     * @inheritdoc
     *
     * @since [*next-version*]
     */
    public function setValue(EntityInterface $entity, $value)
    {
        return $entity->set([$this->key => $value]);
    }
}
