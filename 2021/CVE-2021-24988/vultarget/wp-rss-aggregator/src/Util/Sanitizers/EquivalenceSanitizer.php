<?php

namespace RebelCode\Wpra\Core\Util\Sanitizers;

use RebelCode\Wpra\Core\Util\SanitizerInterface;

/**
 * A sanitizer that compares values against another static literal value and yields booleans, with support for negated
 * equivalence checking.
 *
 * @since 4.16
 */
class EquivalenceSanitizer implements SanitizerInterface
{
    /**
     * @since 4.16
     *
     * @var mixed
     */
    protected $value;

    /**
     * @since 4.16
     *
     * @var bool
     */
    protected $notEqual;

    /**
     * Constructor.
     *
     * @since 4.16
     *
     * @param mixed $value    The value to compare against.
     * @param bool  $notEqual Whether to use negated equivalence.
     */
    public function __construct($value, $notEqual = false)
    {
        $this->value = $value;
        $this->notEqual = $notEqual;
    }

    /**
     * @inheritdoc
     *
     * @since 4.16
     */
    public function sanitize($value)
    {
        return $value === $this->value xor $this->notEqual;
    }
}
