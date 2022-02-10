<?php

namespace RebelCode\Wpra\Core\Entities\Properties;

use RebelCode\Entities\Api\EntityInterface;
use RebelCode\Entities\Api\PropertyInterface;
use RebelCode\Entities\Properties\AbstractDecoratorProperty;

/**
 * A decorator property that translates a datetime string property into a timestamp when reading and writes timestamps
 * as date time strings.
 *
 * @since 4.16
 */
class TimestampProperty extends AbstractDecoratorProperty
{
    /**
     * @since 4.16
     *
     * @var string
     */
    protected $format;

    /**
     * Constructor.
     *
     * @since 4.16
     *
     * @param PropertyInterface $property The property instance to decorate.
     * @param string            $format   The datetime format to use when writing to the data store.
     */
    public function __construct(PropertyInterface $property, $format)
    {
        parent::__construct($property);

        $this->format = $format;
    }

    /**
     * @inheritdoc
     *
     * @since 4.16
     */
    protected function getter(EntityInterface $entity, $prev)
    {
        return strtotime($prev);
    }

    /**
     * @inheritdoc
     *
     * @since 4.16
     */
    protected function setter(EntityInterface $entity, $value)
    {
        return gmdate($this->format, $value);
    }
}
