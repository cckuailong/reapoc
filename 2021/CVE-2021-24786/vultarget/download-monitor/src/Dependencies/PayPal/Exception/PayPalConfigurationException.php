<?php

namespace Never5\DownloadMonitor\Dependencies\PayPal\Exception;

/**
 * Class PayPalConfigurationException
 *
 * @package Never5\DownloadMonitor\Dependencies\PayPal\Exception
 */
class PayPalConfigurationException extends \Exception
{

    /**
     * Default Constructor
     *
     * @param string|null $message
     * @param int  $code
     */
    public function __construct($message = null, $code = 0)
    {
        parent::__construct($message, $code);
    }
}
