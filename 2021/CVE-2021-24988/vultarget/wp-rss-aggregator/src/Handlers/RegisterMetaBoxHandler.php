<?php

namespace RebelCode\Wpra\Core\Handlers;

use WP_Screen;

/**
 * A generic handler for registering WordPress meta boxes.
 *
 * @since 4.14
 */
class RegisterMetaBoxHandler
{
    /**
     * @since 4.14
     */
    const CONTEXT_NORMAL = 'normal';

    /**
     * @since 4.14
     */
    const CONTEXT_ADVANCED = 'advanced';

    /**
     * @since 4.14
     */
    const CONTEXT_SIDE = 'side';

    /**
     * @since 4.14
     */
    const PRIORITY_DEFAULT = 'default';

    /**
     * @since 4.14
     */
    const PRIORITY_LOW = 'low';

    /**
     * @since 4.14
     */
    const PRIORITY_HIGH = 'high';

    /**
     * @since 4.14
     *
     * @var string
     */
    protected $id;

    /**
     * @since 4.14
     *
     * @var string
     */
    protected $title;

    /**
     * @since 4.14
     *
     * @var callable
     */
    protected $callback;

    /**
     * @since 4.14
     *
     * @var string
     */
    protected $screen;

    /**
     * @since 4.14
     *
     * @var string|array|WP_Screen
     */
    protected $context;

    /**
     * @since 4.14
     *
     * @var string
     */
    protected $priority;

    /**
     * @since 4.14
     *
     * @var array
     */
    protected $args;

    /**
     * Constructor.
     *
     * @since 4.14
     *
     * @param string                 $id       The meta box ID.
     * @param string                 $title    The title of the meta box.
     * @param callable               $callback The callback that renders the contents of the meta box.
     * @param string|array|WP_Screen $screen   The screen(s) on which to add the meta box.
     * @param string                 $context  The meta box context. See the `CONTEXT_*` constants in this class.
     * @param string                 $priority The meta box priority. See the `PRIORITY_*` constants in this class.
     * @param array                  $args     Additional arguments to pass to the render callback.
     */
    public function __construct(
        $id,
        $title,
        callable $callback,
        $screen,
        $context = self::CONTEXT_NORMAL,
        $priority = self::PRIORITY_DEFAULT,
        array $args = []
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->callback = $callback;
        $this->screen = $screen;
        $this->context = $context;
        $this->priority = $priority;
        $this->args = $args;
    }

    /**
     * @inheritdoc
     *
     * @since 4.14
     */
    public function __invoke()
    {
        add_meta_box(
            $this->id,
            $this->title,
            $this->callback,
            $this->screen,
            $this->context,
            $this->priority,
            $this->args
        );
    }
}
