<?php

namespace RebelCode\Wpra\Core\Util\Sanitizers;

use RebelCode\Wpra\Core\Util\SanitizerInterface;

/**
 * A sanitizer implementation that sanitizes integer values, with configuration for range control and a default value.
 *
 * @since 4.16
 */
class IntSanitizer implements SanitizerInterface
{
    /**
     * @since 4.16
     *
     * @var int
     */
    protected $default;

    /**
     * @since 4.16
     *
     * @var int|null
     */
    protected $min;

    /**
     * @since 4.16
     *
     * @var int|null
     */
    protected $max;

    /**
     * Constructor.
     *
     * @since 4.16
     *
     * @param int      $default The default value to return if a value is not an integer or is not inside the range.
     * @param int|null $min     Optional minimum value for range control.
     * @param int|null $max     Optional maximum value for range control.
     */
    public function __construct($default = 0, $min = null, $max = null)
    {
        $this->default = $default;
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * @inheritdoc
     *
     * @since 4.16
     */
    public function sanitize($value)
    {
        if (!is_int($value) && !is_numeric($value)) {
            return $this->default;
        }

        $int = (int) $value;
        $int = ($this->min !== null) ? max($this->min, $int) : $int;
        $int = ($this->max !== null) ? min($this->max, $int) : $int;

        return $int;
    }
}
