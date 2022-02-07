<?php

namespace Never5\DownloadMonitor\Dependencies\PayPal\Exception;

/**
 * Class PayPalInvalidCredentialException
 *
 * @package Never5\DownloadMonitor\Dependencies\PayPal\Exception
 */
class PayPalInvalidCredentialException extends \Exception
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

    /**
     * prints error message
     *
     * @return string
     */
    public function errorMessage()
    {
        $errorMsg = 'Error on line ' . $this->getLine() . ' in ' . $this->getFile()
            . ': <b>' . $this->getMessage() . '</b>';
        return $errorMsg;
    }
}
