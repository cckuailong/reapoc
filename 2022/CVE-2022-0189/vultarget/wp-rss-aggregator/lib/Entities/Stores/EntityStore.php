<?php

namespace RebelCode\Entities\Stores;

use OutOfBoundsException;
use RebelCode\Entities\Api\EntityInterface;
use RebelCode\Entities\Api\StoreInterface;

/**
 * A data store implementation that uses an entity as its storage medium.
 *
 * @since [*next-version*]
 */
class EntityStore implements StoreInterface
{
    /**
     * @since [*next-version*]
     *
     * @var EntityInterface
     */
    protected $entity;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param EntityInterface $entity
     */
    public function __construct(EntityInterface $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @inheritdoc
     *
     * @since [*next-version*]
     */
    public function get($key)
    {
        return $this->entity->get($key);
    }

    /**
     * @inheritdoc
     *
     * @since [*next-version*]
     */
    public function has($key)
    {
        try {
            $this->entity->get($key);

            return true;
        } catch (OutOfBoundsException $exception) {
            return false;
        }
    }

    /**
     * @inheritdoc
     *
     * @since [*next-version*]
     */
    public function set(array $data)
    {
        $instance = clone $this;
        $instance->entity = $this->entity->set($data);

        return $instance;
    }
}
