<?php

namespace RebelCode\Wpra\Core\Container;

use Interop\Container\ContainerInterface;

/**
 * A container implementation that wraps around another container to additionally pass its service results through
 * WordPress filters with hook names equal to the service keys.
 *
 * @since 4.13
 */
class WpFilterContainer implements ContainerInterface
{
    /**
     * The inner container.
     *
     * @since 4.13
     *
     * @var ContainerInterface
     */
    protected $inner;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param ContainerInterface $inner The inner container.
     */
    public function __construct(ContainerInterface $inner)
    {
        $this->inner = $inner;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function get($id)
    {
        return apply_filters($id, $this->inner->get($id));
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function has($id)
    {
        return $this->inner->has($id);
    }
}
