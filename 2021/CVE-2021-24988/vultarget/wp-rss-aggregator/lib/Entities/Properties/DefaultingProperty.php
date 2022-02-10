<?php

namespace RebelCode\Entities\Properties;

use OutOfBoundsException;
use RebelCode\Entities\Api\EntityInterface;
use RebelCode\Entities\Api\PropertyInterface;

/**
 * An implementation of a property that will default to a series of data store keys until a valid value is found.
 *
 * @since [*next-version*]
 */
class DefaultingProperty implements PropertyInterface
{
    /**
     * @since [*next-version*]
     *
     * @var array
     */
    protected $keys;

    /**
     * @since [*next-version*]
     *
     * @var callable
     */
    protected $fn;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param array    $keys
     * @param callable $fn
     */
    public function __construct(array $keys, callable $fn = null)
    {
        $this->keys = $keys;
        $this->fn = is_callable($fn)
            ? $fn
            : function ($v) {
                return empty($v);
            };
    }

    /**
     * @inheritdoc
     *
     * @since [*next-version*]
     */
    public function getValue(EntityInterface $entity)
    {
        $store = $entity->getStore();

        foreach ($this->keys as $key) {
            try {
                $value = $store->get($key);

                if (call_user_func_array($this->fn, [$value])) {
                    continue;
                }
            } catch (OutOfBoundsException $exception) {
                continue;
            }

            return $value;
        }

        throw new OutOfBoundsException('No value exists for: ' . implode(', ', $this->keys));
    }

    /**
     * @inheritdoc
     *
     * @since [*next-version*]
     */
    public function setValue(EntityInterface $entity, $value)
    {
        $key = reset($this->keys);

        return [$key => $value];
    }
}
