<?php

namespace RebelCode\Wpra\Core\Query;

use Iterator;
use WP_Post;

/**
 * Abstract implementation of a WP Query iterator.
 *
 * @since 4.13
 */
abstract class AbstractWpQueryIterator implements Iterator
{
    /**
     * The queried posts.
     *
     * @since 4.13
     *
     * @var WP_Post[]
     */
    protected $posts;

    /**
     * Retrieves the WordPress query args.
     *
     * @since 4.13
     *
     * @return array
     */
    abstract protected function getQueryArgs();

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function rewind()
    {
        $this->posts = get_posts($this->getQueryArgs());
        reset($this->posts);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function next()
    {
        next($this->posts);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function key()
    {
        return key($this->posts);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function current()
    {
        return current($this->posts);
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function valid()
    {
        return key($this->posts) !== null;
    }
}
