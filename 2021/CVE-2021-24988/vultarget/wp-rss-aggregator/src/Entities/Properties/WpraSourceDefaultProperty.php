<?php

namespace RebelCode\Wpra\Core\Entities\Properties;

use OutOfBoundsException;
use RebelCode\Entities\Api\EntityInterface;
use RebelCode\Entities\Api\PropertyInterface;

/**
 * A property implementation built specifically for feed items that defaults to a value from the item's feed source.
 *
 * The defaulting mechanism only works for read operations. All write operations are made to the feed item's property.
 *
 * @since 4.16
 */
class WpraSourceDefaultProperty implements PropertyInterface
{
    /**
     * @since 4.16
     *
     * @var string
     */
    protected $key;

    /**
     * @since 4.16
     *
     * @var string
     */
    protected $feedKey;

    /**
     * Constructor.
     *
     * @since 4.16
     *
     * @param string $key     The data store key to read from and write to.
     * @param string $feedKey The key in feed source entities to default to.
     */
    public function __construct($key, $feedKey)
    {
        $this->key = $key;
        $this->feedKey = $feedKey;
    }

    /**
     * @inheritdoc
     *
     * @since 4.16
     */
    public function getValue(EntityInterface $entity)
    {
        try {
            $value = $entity->getStore()->get($this->key);

            if (!empty($value)) {
                return $value;
            }
        } catch (OutOfBoundsException $exception) {
            // Do nothing
        }

        $feed = get_post($entity->get('source_id'));

        if (!property_exists($feed, $this->feedKey)) {
            throw new OutOfBoundsException("Item's feed source does not have property \"{$this->feedKey}\"");
        }

        return $feed->{$this->feedKey};
    }

    /**
     * @inheritdoc
     *
     * @since 4.16
     */
    public function setValue(EntityInterface $entity, $value)
    {
        return [$this->key => $value];
    }
}
