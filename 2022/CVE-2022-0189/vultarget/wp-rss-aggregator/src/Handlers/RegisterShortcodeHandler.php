<?php

namespace RebelCode\Wpra\Core\Handlers;

/**
 * A generic handler implementation that registers a WordPress shortcode.
 *
 * @since 4.13
 */
class RegisterShortcodeHandler
{
    /**
     * The name of the shortcode, or a list of names.
     *
     * @since 4.13
     *
     * @var string|string[]
     */
    protected $name;

    /**
     * The shortcode callback handler.
     *
     * @since 4.13
     *
     * @var callable
     */
    protected $handler;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param string|string[] $name The name of the shortcode, or a list of names.
     * @param callable        $handler The shortcode callback handler.
     */
    public function __construct($name, callable $handler)
    {
        $this->name = $name;
        $this->handler = $handler;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function __invoke()
    {
        foreach ((array) $this->name as $name) {
            add_shortcode($name, $this->handler);
        }
    }
}
