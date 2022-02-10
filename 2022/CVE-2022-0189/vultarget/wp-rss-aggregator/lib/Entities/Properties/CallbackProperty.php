<?php

namespace RebelCode\Entities\Properties;

use RebelCode\Entities\Api\EntityInterface;
use RebelCode\Entities\Api\PropertyInterface;

/**
 * Implementation of a property that uses callbacks for reading and writing values.
 *
 * @since [*next-version*]
 */
class CallbackProperty implements PropertyInterface
{
    /**
     * @since [*next-version*]
     *
     * @var callable
     */
    protected $getter;

    /**
     * @since [*next-version*]
     *
     * @var callable
     */
    protected $setter;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param callable $getter The getter callback. Receives the entity instance as arguments and should return a value.
     * @param callable $setter The setter callback. Receives the entity instance and value as arguments and should
     *                         return a commit array.
     */
    public function __construct(callable $getter, callable $setter)
    {
        $this->getter = $getter;
        $this->setter = $setter;
    }

    /**
     * @inheritdoc
     *
     * @since [*next-version*]
     */
    public function getValue(EntityInterface $entity)
    {
        return call_user_func_array($this->getter, [$entity]);
    }

    /**
     * @inheritdoc
     *
     * @since [*next-version*]
     */
    public function setValue(EntityInterface $entity, $value)
    {
        return call_user_func_array($this->setter, [$entity, $value]);
    }
}
