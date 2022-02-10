<?php

namespace Aventura\Wprss\Core\Model\Event;

/**
 * @since 4.8.1
 */
interface EventInterface
{
    /**
     * Get the event name.
     * 
     * @since 4.8.1
     */
    public function getName();

    /**
     * Get event data.
     *
     * @since 4.8.1
     * @param string|null $key All event data, or data at a specific index.\
     */
    public function getData($key = null);

    /**
     * Set event data.
     *
     * @since 4.8.1
     * @param array|string $key The key to set the value for, or an array of data to replace.
     * @param mixed|null $value The value to set for the data key.
     */
    public function setData($key, $value = null);
}