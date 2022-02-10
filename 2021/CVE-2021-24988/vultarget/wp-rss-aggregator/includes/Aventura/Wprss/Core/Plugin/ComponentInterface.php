<?php

namespace Aventura\Wprss\Core\Plugin;

use Aventura\Wprss\Core;

/**
 * Something that can be a plugin component.
 *
 * A plugin component is something that is a part of a plugin, and therefore
 * interacts with the plugin's main class, i.e. depends on it.
 *
 * @since 4.8.1
 */
interface ComponentInterface
{

    /**
     * Get this component's plugin;
     *
     * @since 4.8.1
     * @return \Aventura\Wprss\Core\Plugin\PluginInterface The instance of the add-on,
     *  of which this is a component.
     */
    public function getPlugin();

    /**
     * Allows the environment to cause this plugin to hook itself into the environment.
     *
     * Called by the environment at a time that is determined to be suitable for the environment.
     *
     * @since 4.8.1
     */
    public function hook();

    /**
     * @since 4.8.1
     * @see Core\Model\LoggerInterface
     */
    public function log($level, $message, array $context = array());

    /**
     * Listen to an event.
     *
     * @since 4.8.1
     * @param string $name The name of the event to listen for.
     * @param callable|string $listener The listener for the event. If string given, the method of this instane
     *  with that name will be used.
     * @param array|null $data Additional arguments to pass to the event.
     * @param int|null $priority Priority of the listener. If null, implementation-default will be used.
     * @param int|null $acceptedArgs Number of the args passed to the listener. If null, implementation-default is used.
     * @return bool True if listener registered; false otherwise.
     */
    public function on($name, $listener, $data = null, $priority = null, $acceptedArgs = null);

    /**
     * Raise an event.
     *
     * @since 4.8.1
     * @param string $name Name of the event.
     * @param array|object $data The event data.
     * @return EventInterface|null An event object that is the result of this event.
     *  This object will contain the data passed, and possibly modified.
     *  If event cannot be raised, null is returned.
     */
    public function event($name, $data = array());
}