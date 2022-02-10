<?php

namespace Aventura\Wprss\Core\Http\Message\Ajax;

use Aventura\Wprss\Core;

/**
 * @since 4.9
 */
interface AjaxInterface extends Core\Http\Message\ResponseInterface
{
    /**
     * Set one or many AJAX data members.
     *
     * This is data particular to the AJAX response, and is separate from the
     * message data.
     *
     * Existing keys will be preserved. Same keys will overwrite.
     *
     * @since 4.9
     * @see Core\DataObjectInterface::setData()
     * @param string|array $key The key to set the data for, or a data array,
     *  where keys are data keys, and values are data values. If array, the
     *  second parameter will be ignored.
     * @param null|mixed $value The value to set for the key.
     */
    public function setAjaxData($key, $value = null);

    /**
     * Get one or all AJAX data members.
     *
     * @since 4.9
     * @param string|null $key The key for which to get data. If null, an array
     *  containing all AJAX data is returned.
     * @return array|mixed The data member value, or an array with all AJAX data.
     */
    public function getAjaxData($key = null);
}
