<?php

namespace RebelCode\Wpra\Core\Entities\Properties;

use RebelCode\Entities\Api\EntityInterface;
use RebelCode\Entities\Api\PropertyInterface;
use RebelCode\Entities\Properties\Property;

/**
 * A property implementation that switches delegation between two properties based on the post type.
 *
 * On get, the post type of entity is checked using an ID property. If the post type is "wprss_feed_item", the "item"
 * property's value is returned. If the post type is anything else, the "post" property's value is returned.
 *
 * This implementation can be configured to save values to either the "item" or the "post" properties.
 *
 * @since 4.17
 */
class WpraPostTypeDependentProperty implements PropertyInterface
{
    /**
     * @since 4.17
     *
     * @var PropertyInterface
     */
    protected $idProp;

    /**
     * @since 4.17
     *
     * @var PropertyInterface
     */
    protected $itemProp;

    /**
     * @since 4.17
     *
     * @var PropertyInterface
     */
    protected $postProp;

    /**
     * @since 4.17
     *
     * @var bool
     */
    protected $setToPost;

    /**
     * Constructor.
     *
     * @since 4.17
     *
     * @param Property          $idProp    The property for reading the post ID.
     * @param PropertyInterface $itemProp  The feed item property.
     * @param PropertyInterface $postProp  The post property.
     * @param bool              $setToPost True to set values to the post property, false to set values to the feed item
     *                                     property. Default is false.
     */
    public function __construct(
        Property $idProp,
        PropertyInterface $itemProp,
        PropertyInterface $postProp,
        $setToPost = false
    ) {
        $this->idProp = $idProp;
        $this->itemProp = $itemProp;
        $this->postProp = $postProp;
        $this->setToPost = $setToPost;
    }

    /**
     * @inheritdoc
     *
     * @since 4.17
     */
    public function getValue(EntityInterface $entity)
    {
        $id = $this->idProp->getValue($entity);
        $type = get_post_type($id);

        return ($type === 'wprss_feed_item')
            ? $this->itemProp->getValue($entity)
            : $this->postProp->getValue($entity);
    }

    /**
     * @inheritdoc
     *
     * @since 4.17
     */
    public function setValue(EntityInterface $entity, $value)
    {
        if ($this->setToPost) {
            return $this->postProp->setValue($entity, $value);
        }

        return $this->itemProp->setValue($entity, $value);
    }
}
