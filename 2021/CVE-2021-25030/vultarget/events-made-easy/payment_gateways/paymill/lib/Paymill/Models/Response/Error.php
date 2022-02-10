<?php

namespace Paymill\Models\Response;

/**
 * Error
 *
 * @category   PayIntelligent
 * @copyright  Copyright (c) 2011 PayIntelligent GmbH (http://payintelligent.de)
 */
class Error
{
    /**
     * @var string
     */
    private $_errorMessage;
    /**
     * @var int
     */
    private $_responseCode;

    /**
     * @var int
     */
    private $_httpStatusCode;

    /**
     * @var \Paymill\Models\Response\Base
     */
    private $_rawObject;

    /**
     * @var array
     */
    private $_errorResponseArray;

    /**
     * Error constructor.
     */
    public function __construct()
    {
        $this->_errorResponseArray = array();
    }

    /**
     * Returns the error message stored in the model
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->_errorMessage;
    }

    /**
     * Sets the error message stored in this model
     * @param string $errorMessage
     * @return \Paymill\Models\Response\Error
     */
    public function setErrorMessage($errorMessage)
    {
        $this->_errorMessage = $errorMessage;
        return $this;
    }

    /**
     * Returns the response code
     * @return int
     */
    public function getResponseCode()
    {
        return $this->_responseCode;
    }

    /**
     * Sets the response code
     * @param int $responseCode
     * @return \Paymill\Models\Response\Error
     */
    public function setResponseCode($responseCode)
    {
        $this->_responseCode = $responseCode;
        return $this;
    }

    /**
     * Returns the status code
     * @return int
     */
    public function getHttpStatusCode()
    {
        return $this->_httpStatusCode;
    }

    /**
     * Sets the status code
     * @param int $httpStatusCode
     * @return \Paymill\Models\Response\Error
     */
    public function setHttpStatusCode($httpStatusCode)
    {
        $this->_httpStatusCode = $httpStatusCode;
        return $this;
    }

    /**
     * Sets the raw object
     * @param \Paymill\Models\Response\Base $rawObject
     * @return \Paymill\Models\Response\Error
     */
    public function setRawObject($rawObject)
    {
        $this->_rawObject = $rawObject;
        return $this;
    }

    /**
     * Returns the raw object
     * @return \Paymill\Models\Response\Base
     */
    public function getRawObject()
    {
        return $this->_rawObject;
    }

    /**
     * Sets raw error response array
     * @param array $error
     * @return \Paymill\Models\Response\Error
     */
    public function setErrorResponseArray(array $error)
    {
        $this->_errorResponseArray = $error;
        return $this;
    }

    /**
     * Returns the raw error response array
     * @return array
     */
    public function getErrorResponseArray()
    {
        return $this->_errorResponseArray;
    }
}
