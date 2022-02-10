<?php

namespace RebelCode\Wpra\Core\Entities\Properties;

use RebelCode\Entities\Api\EntityInterface;
use RebelCode\Entities\Api\PropertyInterface;
use RebelCode\Entities\Properties\AbstractDecoratorProperty;
use RebelCode\Wpra\Core\Util\SanitizerInterface;

/**
 * An implementation of a property that decorates another property and uses a sanitizer.
 *
 * @since 4.16
 */
class SanitizedProperty extends AbstractDecoratorProperty
{
    /**
     * @since 4.16
     *
     * @var SanitizerInterface
     */
    protected $sanitizer;

    /**
     * Constructor.
     *
     * @since 4.16
     *
     * @param PropertyInterface  $property  The original property instance.
     * @param SanitizerInterface $sanitizer The sanitizer to use after reading and before writing.
     */
    public function __construct(PropertyInterface $property, SanitizerInterface $sanitizer)
    {
        parent::__construct($property);

        $this->sanitizer = $sanitizer;
    }

    /**
     * @inheritdoc
     *
     * @since 4.16
     */
    public function getter(EntityInterface $entity, $prev)
    {
        return $this->sanitizer->sanitize($prev);
    }

    /**
     * @inheritdoc
     *
     * @since 4.16
     */
    public function setter(EntityInterface $entity, $value)
    {
        return $this->sanitizer->sanitize($value);
    }
}
