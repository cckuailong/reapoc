<?php

namespace Aventura\Wprss\Core\Model\Event;

/**
 * An interface for something that can manage events.
 *
 * @since 4.8.1
 */
interface EventManagerInterface
{
    /**
     * Add an event listener.
     *
     * @since 4.8.1
     * @param string $name The name of the event.
     * @param callable $listener The listener of the event.
     * @param null|int $priority Priority of the listener. If null, implementation-default will be used.
     * @param null|int $numArgs Number of arguments to pass to the listener. If null, implementation-default will be used.
     */
    public function on($name, $listener, $args = null, $priority = null, $numArgs = null);

    /**
     * Raises an event.
     *
     * @since 4.8.1
     * @param string $name Name of the event.
     * @param array|object $data The event data.
     * @return EventInterface An event object that is the result of this event.
     *  This object will contain the data passed, and possibly modified.
     */
    public function event($name, $data = array());
}