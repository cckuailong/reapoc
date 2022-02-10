<?php

namespace RebelCode\Wpra\Core\Entities\Properties;

use RebelCode\Entities\Api\EntityInterface;
use RebelCode\Entities\Api\PropertyInterface;
use RebelCode\Entities\Api\SchemaInterface;
use RebelCode\Entities\Entity;
use RebelCode\Entities\Stores\ArrayStore;
use RebelCode\Wpra\Core\Entities\Stores\WpPostStore;
use WP_Post;

/**
 * A property implementation that transforms a post ID into an entity instance when reading, and vice-versa when
 * writing.
 *
 * @since 4.16
 */
class WpPostEntityProperty implements PropertyInterface
{
    /**
     * @since 4.16
     *
     * @var string
     */
    protected $idKey;

    /**
     * @since 4.16
     *
     * @var SchemaInterface
     */
    protected $schema;

    /**
     * @since 4.16
     *
     * @var callable|null
     */
    protected $factory;

    /**
     * Constructor.
     *
     * @since 4.16
     *
     * @param string          $idKey   The data store key where the post ID is stored.
     * @param SchemaInterface $schema  The schema to use for created entities.
     * @param callable|null   $factory Optional factory function to use for created entities. If null, {@link Entity}
     *                                 instances will be created. The function will receive the schema and store as
     *                                 arguments.
     */
    public function __construct($idKey, SchemaInterface $schema, callable $factory = null)
    {
        $this->idKey = $idKey;
        $this->schema = $schema;
        $this->factory = $factory;
    }

    /**
     * @inheritdoc
     *
     * @since 4.16
     */
    public function getValue(EntityInterface $entity)
    {
        $id = $entity->getStore()->get($this->idKey);
        $post = get_post($id);
        $store = ($post instanceof WP_Post)
            ? new WpPostStore($post)
            : new ArrayStore([]);

        if ($this->factory === null) {
            return new Entity($this->schema, $store);
        }

        return call_user_func_array($this->factory, [$this->schema, $store]);
    }

    /**
     * @inheritdoc
     *
     * @since 4.16
     */
    public function setValue(EntityInterface $entity, $value)
    {
        if (is_numeric($value)) {
            return [$this->idKey => $value];
        }

        if ($value instanceof WP_Post) {
            return [$this->idKey => $value->ID];
        }

        if ($value instanceof EntityInterface) {
            return [$this->idKey => $value->getStore()->get('ID')];
        }

        return [];
    }
}
