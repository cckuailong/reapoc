<?php

namespace Aventura\Wprss\Core\Plugin;

use Aventura\Wprss\Core;

/**
 * Something that can represent a WP plugin.
 *
 * @since 4.8.1
 */
interface PluginInterface
{
    /**
     * The plugin's basename, e.g. 'my-plugin/my-plugin.php'.
     *
     * @since 4.8.1
     */
    public function getBasename();

    /**
     * The plugin's text domain.
     *
     * This will be used for translation.
     *
     * @since 4.8.1
     */
    public function getTextDomain();

    /**
     * The human-readable name of the plugin.
     *
     * @since 4.8.1
     */
    public function getName();

    /**
     * The unique identifier of the plugin.
     *
     * @since 4.8.1
     */
    public function getCode();

    /**
     * The version number of the plugin.
     *
     * @since 4.8.1
     */
    public function getVersion();
    
    /**
     * @since 4.8.1
     * @return ComponentFactoryInterface The factory used by this add-on to create component instances.
     */
    public function getFactory();

    /**
     * @since 4.8.1
     * @return bool Whether or not the log entry has been processed
     */
    public function log($level, $message, array $context = array());

    /**
     * @since 4.8.1
     * @return Core\Model\LoggerInterface
     */
    public function getLogger();

    /**
     * Creates an exception instance.
     *
     * @since 4.8.1
     * @param string $text The message text.
     * @param string $class The class name of the exception to throw.
     * @param string|null $translate Something that would be used to translate the message.
     * @return \Exception An exception instance.
     */
    public function exception($text, $class = null, $translate = null);

    /**
     * Override this, and inside this method hook into the environment
     *
     * @since 4.8.1
     */
    public function hook();


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
     *  If additional data members were passed with {@see PluginInterface::on()}, members passed here will override.
     * @return Core\Model\Event\EventInterface
     */
    public function event($name, $data = array());

    /**
     * Get the instance of the event manager that the plugin uses.
     *
     * @since 4.8.1
     * @return Core\Model\Event\EventManagerInterface
     */
    public function getEventManager();


    /**
     * Get this instance's event prefix, or a prefixed event name.
     *
     * An event prefix is a prefix that will by default be added to names of events
     * that are listened to or raised by this instance.
     *
     * The event prefix is by default the plugin code followed by an underscore "_", unless the code is
     * not set, in which case the prefix is empty.
     *
     * Note: this method had to be commented out due to a conflict with `Aventura\Wprss\Core\Model\ModelInterface#getEventPrefix()`.
     * This conflicting behaviour can be observed when using PHP version less than 5.3.9.
     * 
     * @todo Uncomment when minimum PHP version requirement is raised above 5.3.9
     * @since 4.8.1
     * @param string|null $name An event name to prefix.
     * @return string This instance's event prefix, or a prefixed name.
     */
    // public function getEventPrefix($name = null);
}