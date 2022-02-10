<?php

namespace RebelCode\Wpra\Core\Entities\Properties;

use RebelCode\Entities\Api\EntityInterface;
use RebelCode\Entities\Api\PropertyInterface;

/**
 * An implementation of a property that transforms a WordPress featured image ID into a URL when reading, and
 * vice-versa when writing.
 *
 * @since 4.16
 */
class WpFtImageUrlProperty implements PropertyInterface
{
    /**
     * @since 4.16
     *
     * @var string
     */
    protected $ftImageIdKey;

    /**
     * @since 4.17
     *
     * @var string
     */
    protected $metaFallback;

    /**
     * Constructor.
     *
     * @since 4.16
     *
     * @param string $ftImageIdKey The data store key where the featured image ID is stored.
     * @param string $metaFallback Optional meta key to fallback to.
     */
    public function __construct($ftImageIdKey, $metaFallback = '')
    {
        $this->ftImageIdKey = $ftImageIdKey;
        $this->metaFallback = $metaFallback;
    }

    /**
     * @inheritdoc
     *
     * @since 4.16
     */
    public function getValue(EntityInterface $entity)
    {
        $store = $entity->getStore();

        $ftImageId = $store->has($this->ftImageIdKey)
            ? $store->get($this->ftImageIdKey)
            : null;

        if (empty($ftImageId)) {
            return !empty($this->metaFallback) && $store->has($this->metaFallback)
                ? $store->get($this->metaFallback)
                : null;
        }

        return wp_get_attachment_image_url($ftImageId, '');
    }

    /**
     * @inheritdoc
     *
     * @since 4.16
     */
    public function setValue(EntityInterface $entity, $value)
    {
        $id = wpra_get_attachment_id_from_url($value);

        if (is_numeric($id) && $id > 0) {
            return [$this->ftImageIdKey => $id];
        }

        return [];
    }
}
