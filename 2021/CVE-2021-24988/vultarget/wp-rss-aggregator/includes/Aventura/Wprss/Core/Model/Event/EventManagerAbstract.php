<?php

namespace Aventura\Wprss\Core\Model\Event;

use Aventura\Wprss\Core;

/**
 * @since 4.8.1
 */
class EventManagerAbstract extends Core\Model\ModelAbstract implements EventManagerInterface
{
    /** @since 4.8.1 */
    const DEFAULT_PRIORITY      = 10;

    /** @since 4.8.1 */
    const DEFAULT_ACCEPTED_ARGS = 1;

    /**
     * The array of actions registered with WordPress.
     *
     * @since 4.8.1
     * @var   array    $actions    The actions registered with WordPress to fire when the plugin loads.
     */
    protected $_events = array();
    protected $_isRan;

    /**
     * Registers an event listener.
     *
     * @since 4.8.1
     * @param string $name The name of the event that a listener is being registered for.
     * @param callable $listener The listener.
     * @param int|null $priority The priority at which the listener should be invoked.
     *  Default: {@link EventManagerAbstract::DEFAULT_PRIORITY}.
     * @param array|null $data
     * @param int|null $acceptedArgs The number of arguments that should be passed to the listener.
     *  Default: {@link EventManagerAbstract::DEFAULT_ACCEPTED_ARGS}.
     *  You don't really need to specify this with events fired via this class.
     * @param bool $now Whether or not to add the event right now, now when run().
     * @return EventManagerAbstract This instance.
     */
    public function on($name, $listener, $data = null, $priority = null, $acceptedArgs = null)
    {
        if (is_null($priority)) {
            $priority = static::DEFAULT_PRIORITY;
        }
        if (is_null($acceptedArgs)) {
            $acceptedArgs = static::DEFAULT_ACCEPTED_ARGS;
        }
        if (is_null($data)) {
            $data = array();
        }

        $listener = $this->_normalizeCallback($listener);
        $eventInfo = array(
            'name'          => $name,
            'listener'      => $listener,
            'args'          => $data,
            'priority'      => $priority,
            'accepted_args' => $acceptedArgs
        );
        if ($this->getIsKeepRecords()) {
            $this->_events[] = $eventInfo;
        }
        $this->_register($eventInfo);

        return $name;
    }

    /**
     * Registers an event with the environment.
     *
     * @since 4.8.1
     * @param array $eventInfo Data of the event.
     * @return EventManagerAbstract This instance.
     */
    protected function _register($eventInfo)
    {
        $proxy = function() use ($eventInfo) {
            $args = func_get_args();

            // Adding registration time arguments
            $event = isset($args[0]) ? $args[0] : null;
            $argsOverride = isset($eventInfo['args']) ? $eventInfo['args'] : array();
            if ($event instanceof EventInterface) {
                foreach ($argsOverride as $_arg => $_argVal) {
                    if (!$event->hasData($_arg)) {
                        $event->setData($_arg, $_argVal);
                    }
                }
            }

            return call_user_func_array($eventInfo['listener'], $args);
        };
        
        add_filter($eventInfo['name'], $proxy, $eventInfo['priority'], $eventInfo['accepted_args']);
        return $this;
    }

    /**
     * Normalizes a callback.
     *
     * @since 4.8.1
     * @param callable $callback The callback to normalize
     * @return If array is given, makes sure that the result is a numeric array with correct order of valuesl
     *  Otherwise, returns the callback unmodified.
     */
    protected function _normalizeCallback($callback)
    {
        if (is_array($callback)) {
            $callback  = array_values($callback);
            $component = $callback[0];
            $callback  = $callback[1];
            $callback = array($component, $callback);
        }

        return $callback;
    }

    /**
     * {@inheritdoc}
     *
     * @since 4.8.1
     */
    public function event($name, $data = array())
    {
        if (is_object($data)) {
            $data = (array) $data;
        }
        $event = $this->_createEvent($name, $data);
        return $this->_dispatch($event);
    }

    /**
     * Dispatches the actual event.
     *
     * @since 4.8.1
     * @param EventInterface $event The event to dispatch.
     * @return mixed The event object, possibly modified.
     */
    protected function _dispatch(EventInterface $event)
    {
        return apply_filters($event->getName(), $event);
    }

    /**
     * Creates a new event.
     *
     * @since 4.8.1
     * @param string $name The name of the event.
     * @param array $args An array of arguments.
     * @return EventInterface
     */
    protected function _createEvent($name, $args = array())
    {
        $event = new Event($name);

        if (!is_array($args)) {
            throw $this->exception('Args must be an array');
        }

        $event->setData($args);

        return $event;
    }
}