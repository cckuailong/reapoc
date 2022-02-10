<?php

namespace RebelCode\Wpra\Core\Util\Sanitizers;

use RebelCode\Wpra\Core\Util\SanitizerInterface;

/**
 * A sanitizer implementation that sanitizes boolean values.
 *
 * @since 4.16
 */
class BoolSanitizer implements SanitizerInterface
{
    /**
     * @inheritdoc
     *
     * @since 4.16
     */
    public function sanitize($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
