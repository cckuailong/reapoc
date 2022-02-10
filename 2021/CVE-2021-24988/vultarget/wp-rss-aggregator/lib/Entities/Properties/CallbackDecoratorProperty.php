<?php

namespace RebelCode\Entities\Properties;

use RebelCode\Entities\Api\EntityInterface;
use RebelCode\Entities\Api\PropertyInterface;

/**
 * A generic callback-based decorator property.
 *
 * @since [*next-version*]
 */
class CallbackDecoratorProperty implements PropertyInterface
{
    /**
     * @since [*next-version*]
     *
     * @var PropertyInterface
     */
    protected $property;

    /**
     * @since [*next-version*]
     *
     * @var callable|null
     */
    protected $getter;

    /**
     * @since [*next-version*]
     *
     * @var callable|null
     */
    protected $setter;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param PropertyInterface $property The property instance to decorate.
     * @param callable|null     $getter   The getter callback. Recieves the entity instance and value as arguments and
     *                                    should return a value. Invoked after the decorated instance's getter.
     * @param callable|null     $setter   The setter callback. Receives the entity instance and value as arguments and
     *                                    should return a commit array. Invoked after the decorated instance's setter.
     */
    public function __construct(PropertyInterface $property, callable $getter = null, callable $setter = null)
    {
        $this->property = $property;
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
        $value = $this->property->getValue($entity);

        return is_callable($this->getter)
            ? call_user_func_array($this->getter, [$entity, $value])
            : $value;
    }

    /**
     * @inheritdoc
     *
     * @since [*next-version*]
     */
    public function setValue(EntityInterface $entity, $value)
    {
        $commit = $this->property->setValue($entity, $value);

        return is_callable($this->setter)
            ? call_user_func_array($this->setter, [$entity, $value, $commit])
            : $commit;
    }
}
