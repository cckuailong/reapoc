<?php

namespace Paymill\Models\Request;

/**
 * Fraud Model
 * This is an experimental feature! API for frauds may change before being marked as stable for production use.
 */
class Fraud extends Base
{
    /**
     * @var string
     */
    private $_identifier = null;

    /**
     * Creates an instance of the fraud request model
     */
    function __construct()
    {
        $this->_serviceResource = 'Frauds/';
    }

    /**
     * Returns the identifier
     * @return string||null
     */
    public function getIdentifier()
    {
        return $this->_identifier;
    }

    /**
     * Sets the identifier
     * @param string $identifier 
     * @return \Paymill\Models\Request\Fraud
     */
    public function setIdentifier($identifier)
    {
        $this->_identifier = $identifier;
        return $this;
    }

    /**
     * Returns an array of parameters customized for the argumented methodname
     * @param string $method
     * @return array
     */
    public function parameterize($method)
    {
        $parameterArray = array();
        switch ($method) {
            case 'create':
                $parameterArray['Identifier'] = $this->getIdentifier();
                break;
            case 'update':
                $parameterArray['Identifier'] = $this->getIdentifier();
                break;
            case 'delete':
                break;
            case 'getOne':
                $parameterArray['count'] = 1;
                $parameterArray['offset'] = 0;
                break;
            case 'getAll':
                $parameterArray = $this->getFilter();
                break;
        }

        return $parameterArray;
    }
}
