<?php

namespace RebelCode\Wpra\Core\Handlers;

/**
 * A handler that loads a plugin's text domain.
 *
 * @since 4.13
 */
class LoadTextDomainHandler
{
    /**
     * The text domain to load.
     *
     * @since 4.13
     *
     * @var string
     */
    protected $domain;

    /**
     * The path to the translation files directory.
     *
     * @since 4.13
     *
     * @var string
     */
    protected $directory;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param string $domain    The text domain to load.
     * @param string $directory The path to the translation files directory.
     */
    public function __construct($domain, $directory)
    {
        $this->domain = $domain;
        $this->directory = $directory;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function __invoke()
    {
        load_plugin_textdomain($this->domain, false, $this->directory);
    }
}
