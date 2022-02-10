<?php

namespace RebelCode\Entities;

use Exception;
use OutOfBoundsException;
use RebelCode\Entities\Api\EntityInterface;
use RebelCode\Entities\Api\PropertyInterface;
use RebelCode\Entities\Api\SchemaInterface;
use RebelCode\Entities\Api\StoreInterface;

/**
 * The standard implementation for an entity, that uses a schema for its configuration and a store for its raw storage.
 *
 * @since [*next-version*]
 */
class Entity implements EntityInterface
{
    /**
     * @since [*next-version*]
     *
     * @var SchemaInterface
     */
    protected $schema;

    /**
     * @since [*next-version*]
     *
     * @var StoreInterface
     */
    protected $store;

    /**
     * @since [*next-version*]
     *
     * @var PropertyInterface[]
     */
    protected $props;

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
     * @param SchemaInterface $schema
     * @param StoreInterface  $store
     */
    public function __construct(SchemaInterface $schema, StoreInterface $store)
    {
        $this->schema = $schema;
        $this->store = $store;
        $this->props = $schema->getProperties();
        $this->defaults = $schema->getDefaults();
    }

    /**
     * @inheritdoc
     *
     * @since [*next-version*]
     */
    public function get($key)
    {
        if (!array_key_exists($key, $this->props)) {
            throw new OutOfBoundsException();
        }

        try {
            return $this->props[$key]->getValue($this);
        } catch (OutOfBoundsException $exception) {
            return array_key_exists($key, $this->defaults)
                ? $this->defaults[$key]
                : null;
        }
    }

    /**
     * @inheritdoc
     *
     * @since [*next-version*]
     */
    public function set(array $data)
    {
        $changeSet = [];

        foreach ($data as $key => $value) {
            if (!array_key_exists($key, $this->props)) {
                throw new OutOfBoundsException(
                    sprintf("Entity does not have key \"%s\" in: %s", $key, json_encode($data))
                );
            }

            try {
                $commit = $this->props[$key]->setValue($this, $value);
            } catch (Exception $exception) {
                continue;
            }

            $changeSet = array_merge($changeSet, $commit);
        }

        // Create a new instance
        $newEntity = clone $this;
        // Set the changes and update the store instance
        $newEntity->store = $this->store->set($changeSet);

        return $newEntity;
    }

    /**
     * @since [*next-version*]
     *
     * @return StoreInterface
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * @since [*next-version*]
     *
     * @return SchemaInterface
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @since [*next-version*]
     *
     * @return array
     */
    public function export()
    {
        $result = [];
        foreach ($this->props as $key => $prop) {
            $result[$key] = $this->get($key);
        }

        return $result;
    }
}
