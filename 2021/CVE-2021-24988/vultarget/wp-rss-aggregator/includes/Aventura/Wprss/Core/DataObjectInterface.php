<?php

namespace Aventura\Wprss\Core;

/**
 * An interface for something that can hold and manipulate arbitrary internal data.
 *
 * @since 4.8.1
 */
interface DataObjectInterface
{
    /**
     * @since 4.8.1
     */
    public function getData($key = null);

    /**
     * @since 4.8.1
     */
    public function hasData($key = null);

    /**
     * @since 4.8.1
     */
    public function unsetData($key = null);

    /**
     * @since 4.8.1
     */
    public function setData($key, $value = null);

    /**
     * @since 4.8.1
     */
    public function setDataUsingMethod($key);
}