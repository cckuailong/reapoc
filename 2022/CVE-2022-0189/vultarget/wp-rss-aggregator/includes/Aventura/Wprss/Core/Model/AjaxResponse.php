<?php

namespace Aventura\Wprss\Core\Model;

use Aventura\Wprss\Core;

/**
 * @since 4.9
 */
class AjaxResponse extends Core\Http\Message\Ajax\AbstractResponse
{
    /**
     * A convenient way to create an instance of this class which indicates that an exception has occurred.
     * 
     * @since 4.9
     * @param \Exception $e An exception, from which to take the response data.
     * @return AjaxResponse A new instance with relevant data from the exception.
     */
    public static function createFromException(\Exception $e)
    {
        $response = static::createFromError($e->getMessage())->setAjaxData(array(
            'error_code'        => $e->getCode()
        ));
        
        if (static::isDebug()) {
            $response->setAjaxData(array(
                'debug_trace'       => $e->getTrace(),
                'debug_file'        => $e->getFile(),
                'debug_line'        => $e->getLine()
            ));
        }
        
        return $response;
    }
    
    /**
     * A convenient way to create an instance of this class whic indicates that an error has occurred.
     * 
     * @since 4.9
     * @param string $error Text of the error message.
     * @return AjaxResponse A new instance with relevant error data.
     */
    public static function createFromError($error)
    {
        $response = new static();
        $response->setAjaxData(array(
            'is_error'          => true,
            'error_message'     => $error
        ));
        
        return $response;
    }
    
    /**
     * Determines if this class is in debug mode.
     * 
     * In debug mode, extended information about exceptions will be added to
     * instances generated from exceptions. This data may not be safe for
     * displaying in production environments.
     * 
     * @since 4.9
     * @return bool True if debug mode is on for this class; false otherwise.
     */
    public static function isDebug()
    {
        return WP_DEBUG;
    }
}
