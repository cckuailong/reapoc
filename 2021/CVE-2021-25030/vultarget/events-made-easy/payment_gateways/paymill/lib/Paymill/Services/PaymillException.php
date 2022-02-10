<?php

namespace Paymill\Services;

use Paymill\Models\Response\Error;

/**
 * PaymillException
 */
class PaymillException extends \Exception
{
    /**
     * Exception error message
     * @var null|string
     */
    private $_errorMessage;

    /**
     * PAYMILL API error code
     * @var int|null
     */
    private $_responseCode;

    /**
     * PAYMILL API http status code
     * @var int|null
     */
    private $_httpStatusCode;

    /**
     * optional resource
     * @var null|\Paymill\Models\Response\Base
     */
    private $_rawObject;

    /**
     * raw error array
     * @var array|null
     */
    private $_rawError;

    /**
     * PaymillException constructor.
     * @param int|null $responseCode
     * @param string|null $message
     * @param int|null $code
     * @param \Paymill\Models\Response\Base|null $rawObject
     * @param array|null $rawError
     */
    public function __construct($responseCode = null, $message = null, $code = null, $rawObject = null, $rawError = null)
    {
        parent::__construct($message, $code, null);
        $this->_errorMessage = $message;
        $this->_responseCode = $responseCode;
        $this->_httpStatusCode = $code;
        $this->_rawObject = $rawObject;
        $this->_rawError = $rawError;
    }

    /**
     * Returns the exception message
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->_errorMessage;
    }

    /**
     * Returns the PAYMILL API http status code
     * @return string
     */
    public function getStatusCode()
    {
        return $this->_httpStatusCode;
    }

    /**
     * Returns the PAYMILL API response code
     * @return integer
     */
    public function getResponseCode()
    {
        return $this->_responseCode;
    }

    /**
     * Returns the additional resource if any
     * @return \Paymill\Models\Response\Base|null
     */
    public function getRawObject()
    {
        return $this->_rawObject;
    }

    /**
     * Returns the raw error array
     * @return array|null
     */
    public function getRawError()
    {
        return $this->_rawError;
    }
}
