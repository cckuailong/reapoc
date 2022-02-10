<?php

namespace RebelCode\Wpra\Core\Handlers;

use stdClass;
use Traversable;

/**
 * A generic handler implementation that invokes a list of children handlers in sequence.
 *
 * @since 4.13
 */
class MultiHandler
{
    /**
     * The list of handlers to invoke.
     *
     * @since 4.13
     *
     * @var callable[]|stdClass|Traversable
     */
    protected $handlers;

    /**
     * Constructor.
     *
     * @since 4.13
     *
     * @param callable[]|stdClass|Traversable $handlers The list of handlers to invoke.
     */
    public function __construct($handlers)
    {
        $this->handlers = $handlers;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.13
     */
    public function __invoke()
    {
        $args = func_get_args();

        foreach ($this->handlers as $handler) {
            call_user_func_array($handler, $args);
        }
    }
}
