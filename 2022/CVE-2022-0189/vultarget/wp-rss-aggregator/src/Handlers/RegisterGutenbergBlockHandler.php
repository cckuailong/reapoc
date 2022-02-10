<?php

namespace RebelCode\Wpra\Core\Handlers;

/**
 * A handler for Gutenberg block registration.
 *
 * @since 4.13
 */
class RegisterGutenbergBlockHandler
{
    /**
     * The name of the block.
     *
     * @since 4.13
     *
     * @var string
     */
    protected $name;

    /**
     * Gutenberg block's configuration.
     *
     * @since 4.13
     *
     * @var array
     */
    protected $config;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param string $name The name of the block.
     * @param array  $config Gutenberg block's configuration.
     */
    public function __construct($name, $config)
    {
        $this->name = $name;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function __invoke()
    {
        /*
         * Register the block only when Gutenberg editor is enabled.
         */
        if (!function_exists('register_block_type')) {
            return;
        }

        register_block_type($this->name, $this->config);
    }
}
