<?php

namespace RebelCode\Wpra\Core\Entities\Properties;

use RebelCode\Entities\Api\EntityInterface;
use RebelCode\Entities\Api\PropertyInterface;

/**
 * A property for WordPress post permalinks. Read-only.
 *
 * @since 4.17
 */
class WpPostPermalinkProperty implements PropertyInterface
{
    /**
     * @since 4.17
     *
     * @var PropertyInterface
     */
    protected $idProp;

    /**
     * Constructor.
     *
     * @since 4.17
     *
     * @param PropertyInterface $idProp The property for the WP Post instance or ID.
     */
    public function __construct(PropertyInterface $idProp)
    {
        $this->idProp = $idProp;
    }

    /**
     * @inheritdoc
     *
     * @since 4.17
     */
    public function getValue(EntityInterface $entity)
    {
        return get_post_permalink($this->idProp->getValue($entity));
    }

    /**
     * @inheritdoc
     *
     * @since 4.17
     */
    public function setValue(EntityInterface $entity, $value)
    {
        return [];
    }
}
