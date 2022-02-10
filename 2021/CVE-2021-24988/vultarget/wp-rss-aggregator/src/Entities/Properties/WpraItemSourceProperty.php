<?php

namespace RebelCode\Wpra\Core\Entities\Properties;

use OutOfBoundsException;
use RebelCode\Entities\Api\EntityInterface;
use RebelCode\Entities\Api\PropertyInterface;

/**
 * A specialized feed item property implementation that falls back to a source property.
 *
 * This property replicates the get behavior of either another feed item property of a property of the feed source.
 * Which one is replicated depends on the value of a boolean "control" property. When the control property has a value
 * of true, the feed item property is used. If the control property has a value of false, or true but the feed item has
 * no value for the property, the feed source property is used instead. All set operations will be handled using the
 * feed item property.
 *
 * @since 4.17
 */
class WpraItemSourceProperty implements PropertyInterface
{
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
    protected $sourceProp;

    /**
     * @since 4.17
     *
     * @var string
     */
    protected $controlProp;

    /**
     * Constructor.
     *
     * @since 4.17
     *
     * @param PropertyInterface $itemProp    The feed item property.
     * @param PropertyInterface $sourceProp  The feed source property.
     * @param string            $controlProp The control property.
     */
    public function __construct(
        PropertyInterface $itemProp,
        PropertyInterface $sourceProp,
        $controlProp
    ) {
        $this->itemProp = $itemProp;
        $this->sourceProp = $sourceProp;
        $this->controlProp = $controlProp;
    }

    /**
     * @inheritdoc
     *
     * @since 4.17
     */
    public function getValue(EntityInterface $item)
    {
        $feed = $item->get('source');

        if ($feed) {
            try {
                $control = $feed->get($this->controlProp);
                $value = $control ? $this->itemProp->getValue($item) : null;

                if (!empty($value)) {
                    return $value;
                }
            } catch (OutOfBoundsException $exception) {
                // Do nothing
            }
        }

        // Default to value from feed source
        return $this->sourceProp->getValue($feed);
    }

    /**
     * @inheritdoc
     *
     * @since 4.17
     */
    public function setValue(EntityInterface $entity, $value)
    {
        return $this->itemProp->setValue($entity, $value);
    }
}
