<?php

namespace RebelCode\Wpra\Core\Query;

/**
 * A simple implementation of a WordPress query iterator.
 *
 * @since 4.13
 */
class WpQueryIterator extends AbstractWpQueryIterator
{
    /**
     * The WP Query arguments.
     *
     * @since 4.13
     *
     * @var array
     */
    protected $args;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param array $args The WP Query arguments.
     */
    public function __construct($args)
    {
        $this->args = $args;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    protected function getQueryArgs()
    {
        return $this->args;
    }
}
