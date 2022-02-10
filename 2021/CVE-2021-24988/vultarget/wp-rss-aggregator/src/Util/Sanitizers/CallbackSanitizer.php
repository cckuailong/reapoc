<?php

namespace RebelCode\Wpra\Core\Util\Sanitizers;

use RebelCode\Wpra\Core\Util\SanitizerInterface;

/**
 * A sanitizer implementation that uses callbacks for sanitization.
 *
 * @since 4.16
 */
class CallbackSanitizer implements SanitizerInterface
{
    /**
     * @since 4.16
     *
     * @var callable
     */
    protected $callback;

    /**
     * Constructor.
     *
     * @since 4.16
     *
     * @param callable $callback The callback function. Recieves the value as argument and should return the
     *                           sanitized value.
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @inheritdoc
     *
     * @since 4.16
     */
    public function sanitize($value)
    {
        return call_user_func_array($this->callback, [$value]);
    }
}
