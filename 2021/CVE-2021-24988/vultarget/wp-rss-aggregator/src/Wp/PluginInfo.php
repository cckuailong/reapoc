<?php

namespace RebelCode\Wpra\Core\Wp;

use ArrayAccess;

class PluginInfo
{
    /**
     * @since 4.13
     *
     * @var string
     */
    public $name;

    /**
     * @since 4.13
     *
     * @var string
     */
    public $slug;

    /**
     * @since 4.13
     *
     * @var string
     */
    public $version;

    /**
     * @since 4.13
     *
     * @var string
     */
    public $tested;

    /**
     * @since 4.13
     *
     * @var string
     */
    public $requires;

    /**
     * @since 4.13
     *
     * @var string
     */
    public $author;

    /**
     * @since 4.13
     *
     * @var string
     */
    public $author_profile;

    /**
     * @since 4.13
     *
     * @var string
     */
    public $download_link;

    /**
     * @since 4.13
     *
     * @var string
     */
    public $trunk;

    /**
     * @since 4.13
     *
     * @var string
     */
    public $last_updated;

    /**
     * @since 4.13
     *
     * @var array
     */
    public $sections;

    /**
     * @since 4.13
     *
     * @var array
     */
    public $banners;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param array|ArrayAccess $info The information array.
     */
    public function __construct($info)
    {
        $this->name = $info['name'];
        $this->slug = $info['slug'];
        $this->version = $info['version'];
        $this->tested = $info['tested'];
        $this->requires = $info['requires'];
        $this->author = $info['author'];
        $this->author_profile = $info['author_profile'];
        $this->download_link = $info['download_link'];
        $this->trunk = $info['trunk'];
        $this->last_updated = $info['last_updated'];
        $this->sections = $info['sections'];
        $this->banners = $info['banners'];
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}
