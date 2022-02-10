<?php

namespace Aventura\Wprss\Core\Model;

/**
 * Anything that can be a model.
 * 
 * A model is something that solves a particular problem, e.g. represents a problem domain.
 *
 * @since 4.8.1
 */
interface ModelInterface
{
    /**
     *
     * @param string $text The text to translate.
     * @param mixed $translator Something that determines how to translate the text.
     */
    public function __($text, $translator = null);

    /**
     * Logs a message
     *
     * @since 4.8.1
     * @return bool True if log entry was processed; false otherwise.
     */
    public function log($level, $message, array $context = array());

    /**
     * Add an event listener.
     *
     * @since 4.8.1
     * @param string $name Event name.
     * @param callable $listener The event listener.
     * @param null|array $data Additional data to be passed to event handlers. May not work on native system events.
     *  If the event gets passed data with same names when raised, they will override data passed here.
     * @param int|null $priority Order priority of the listener. If null, implementation-specific default will be assumed.
     * @param int|null $acceptedArgs The number of arguments to be passed to the handler. If null,
     *  implementation-specific default will be assumed.
     * @return string|bool The eventual name of the event that was used on success; false if listener not registered.
     */
    public function on($name, $listener, $data = null, $priority = null, $acceptedArgs = null);

    /**
     * Raise an event.
     *
     * This triggers all event handlers.
     *
     * @since 4.8.1
     * @param string $name Name of the event to raise
     * @param array $data The data to pass to the event handlers. This will be passed as the first and only argument.
     *  If additional data members were passed with {@see ModelInterface::on()}, members passed here will override.
     * @return Core\Model\Event\EventInterface
     */
    public function event($name, $data = array());

    /**
     * Get this instance's event prefix, or a prefixed event name.
     *
     * An event prefix is a prefix that will by default be added to names of events
     * that are listened to or raised by this instance.
     *
     * The event prefix is by default the plugin code followed by an underscore "_", unless the code is
     * not set, in which case the prefix is empty.
     *
     * @since 4.8.1
     * @param string|null $name An event name to prefix.
     * @return string This instance's event prefix, or a prefixed name.
     */
    public function getEventPrefix($name = null);
}
