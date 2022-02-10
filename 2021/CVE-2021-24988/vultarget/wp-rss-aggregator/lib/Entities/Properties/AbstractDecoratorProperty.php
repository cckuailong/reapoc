<?php

namespace RebelCode\Entities\Properties;

use OutOfBoundsException;
use RebelCode\Entities\Api\EntityInterface;
use RebelCode\Entities\Api\PropertyInterface;

/**
 * Abstract implementation of a property that decorates another property.
 *
 * Implementations will need to implement the {@link getter()} and {@link setter()} methods. The getter is invoked
 * _after_ the original property's value is retrieved, giving this instance a chance to modify the outgoing value.
 * The setter is invoked _before_ the original property's {@link PropertyInterface::setValue()}, giving this instance
 * a chance to modify in value that is given to the original property.
 *
 * @since [*next-version*]
 */
abstract class AbstractDecoratorProperty implements PropertyInterface
{
    /**
     * @since [*next-version*]
     *
     * @var PropertyInterface
     */
    protected $property;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param PropertyInterface $property The property instance to decorate.
     */
    public function __construct(PropertyInterface $property)
    {
        $this->property = $property;
    }

    /**
     * @inheritdoc
     *
     * @since [*next-version*]
     */
    public function getValue(EntityInterface $entity)
    {
        return $this->getter($entity, $this->property->getValue($entity));
    }

    /**
     * @inheritdoc
     *
     * @since [*next-version*]
     */
    public function setValue(EntityInterface $entity, $value)
    {
        return $this->property->setValue($entity, $this->setter($entity, $value));
    }

    /**
     * Retrieves the actual value for the value returned by the original property.
     *
     * @since [*next-version*]
     *
     * @param EntityInterface $entity The entity instance.
     * @param mixed           $value  The value returned by the original property.
     *
     * @return mixed The value.
     */
    abstract protected function getter(EntityInterface $entity, $value);

    /**
     * Retrieves the actual value to set to the original property.
     *
     * @since [*next-version*]
     *
     * @param EntityInterface $entity The entity instance.
     * @param mixed           $value  The value being set.
     *
     * @return mixed The value.
     */
    abstract protected function setter(EntityInterface $entity, $value);
}
