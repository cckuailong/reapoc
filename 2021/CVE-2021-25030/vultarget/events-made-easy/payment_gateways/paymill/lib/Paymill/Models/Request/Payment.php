<?php

namespace Paymill\Models\Request;

/**
 * Payment Model
 * The Payment object represents a payment with a credit card or via direct debit.
 * It is used for several function calls (e.g. transactions, subscriptions, clients, ...).
 * To be PCI compliant these information is encoded by our Paymill PSP. You only get in touch with safe data (token)
 * and needn’t to care about the security problematic of informations like credit card data.
 * @tutorial https://paymill.com/de-de/dokumentation/referenz/api-referenz/#document-payments
 */
class Payment extends Base
{
    /**
     * @var string
     */
    private $_client;
    /**
     * @var string
     */
    private $_token;

    /**
     * Creates an instance of the payment request model
     */
    public function __construct()
    {
        $this->_serviceResource = 'Payments/';
    }

    /**
     * Returns the identifier of a client (client-object)
     * @return string The identifier of a client (client-object)
     */
    public function getClient()
    {
        return $this->_client;
    }

    /**
     * Sets the identifier of a client (client-object)
     * @param string $client
     * @return Payment
     */
    public function setClient($client)
    {
        $this->_client = $client;
        return $this;
    }

    /**
     * Returns the Token
     * @return String
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * Sets the token required for payment creation
     * @param string $token
     * @return Payment
     */
    public function setToken($token)
    {
        $this->_token = $token;
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
                $parameterArray['token'] = $this->getToken();
                $parameterArray['client'] = $this->getClient();
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
