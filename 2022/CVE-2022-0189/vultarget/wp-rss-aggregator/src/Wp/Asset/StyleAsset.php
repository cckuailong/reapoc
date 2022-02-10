<?php

namespace RebelCode\Wpra\Core\Wp\Asset;

/**
 * Class for enqueuing styles.
 *
 * @since 4.14
 */
class StyleAsset extends AbstractAsset
{
    /**
     * The media for which this stylesheet has been defined.
     *
     * @since 4.14
     *
     * @var string
     */
    protected $media;

    /**
     * @inheritdoc
     *
     * @since 4.14
     *
     * @param string $media The media for which this stylesheet has been defined. Accepts media types like 'all',
     *                      'print' and 'screen', or media queries like '(orientation: portrait)' and '(max-width:
     *                      640px)'.
     */
    public function __construct($handle, $src, $deps = [], $version = false, $media = 'all')
    {
        parent::__construct($handle, $src, $deps, $version);

        $this->media = $media;
    }

    /**
     * @inheritdoc
     *
     * @since 4.14
     */
    public function register()
    {
        wp_register_style($this->handle, $this->src, $this->deps, $this->version, $this->media);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.14
     */
    public function enqueue()
    {
        $this->register();
        wp_enqueue_style($this->handle);
    }
}
