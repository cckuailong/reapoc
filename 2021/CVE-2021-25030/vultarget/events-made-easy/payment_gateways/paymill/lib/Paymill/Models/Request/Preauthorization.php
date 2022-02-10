<?php

namespace Paymill\Models\Request;

/**
 * Preauthorization Model
 * If you’d like to reserve some money from the client’s credit card but you’d also like to execute the transaction
 * itself a bit later, then use preauthorizations. This is NOT possible with direct debit.
 * A preauthorization is valid for 7 days.
 */
class Preauthorization extends Base
{
    /**
     * @var string
     */
    private $_amount;

    /**
     * @var string
     */
    private $_currency;

    /**
     * @var string
     */
    private $_payment;

    /**
     * @var string
     */
    private $_token;

    /**
     * Source
     *
     * @var $_source
     */
    private $_source;

    /**
     * @var string
     */
    private $_description;

    /**
     * @var string
     */
    private $_client;

    /**
     * Creates an instance of the preauthorization request model
     */
    function __construct()
    {
        $this->_serviceResource = 'Preauthorizations/';
    }

    /**
     * Returns the amount
     * @return string
     */
    public function getAmount()
    {
        return $this->_amount;
    }

    /**
     * Sets the amount
     * @param string $amount
     * @return \Paymill\Models\Request\Preauthorization
     */
    public function setAmount($amount)
    {
        $this->_amount = $amount;
        return $this;
    }

    /**
     * Returns the currency
     * @return string
     */
    public function getCurrency()
    {
        return $this->_currency;
    }

    /**
     * Sets the currency
     * @param string $currency
     * @return \Paymill\Models\Request\Preauthorization
     */
    public function setCurrency($currency)
    {
        $this->_currency = $currency;
        return $this;
    }

    /**
     * Returns the identifier of a payment
     * @return string
     */
    public function getPayment()
    {
        return $this->_payment;
    }

    /**
     * Sets the identifier of a payment
     * @param string $payment
     * @return \Paymill\Models\Request\Preauthorization
     */
    public function setPayment($payment)
    {
        $this->_payment = $payment;
        return $this;
    }

    /**
     * Returns the token required for the creation of preAuths
     * @return string
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * Sets the token required for the creation of preAuths
     * @param string $token
     * @return \Paymill\Models\Request\Preauthorization
     */
    public function setToken($token)
    {
        $this->_token = $token;
        return $this;
    }

    /**
     * Sets the name of origin of the call creating the transaction.
     *
     * @param string $source Source
     *
     * @return $this
     */
    public function setSource($source)
    {
        $this->_source = $source;

        return $this;
    }

    /**
     * Gets the name of origin of the call creating the transaction.
     *
     * @return string
     */
    public function getSource()
    {
        return $this->_source;
    }

    /**
     * Returns the description
     * @return string
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Sets the description
     * @param string $description
     * @return \Paymill\Models\Request\Preauthorization
     */
    public function setDescription($description)
    {
        $this->_description = $description;
        return $this;
    }

    /**
     * Returns the client
     * @return string
     */
    public function getClient()
    {
        return $this->_client;
    }

    /**
     * Sets the client
     * @param string $client
     * @return \Paymill\Models\Request\Preauthorization
     */
    public function setClient($client)
    {
        $this->_client = $client;
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
                if (!is_null($this->getPayment())) {
                    $parameterArray['payment'] = $this->getPayment();
                } else {
                    $parameterArray['token'] = $this->getToken();
                }
                $parameterArray['amount'] = $this->getAmount();
                $parameterArray['currency'] = $this->getCurrency();
                $parameterArray['description'] = $this->getDescription();
                if (!is_null($this->getClient())) {
                    $parameterArray['client'] = $this->getClient();
                }
                if(!is_null($this->getSource())) {
                    $parameterArray['source'] = $this->getSource();
                }
                break;
            case 'getOne':
                $parameterArray['count'] = 1;
                $parameterArray['offset'] = 0;
                break;
            case 'getAll':
                $parameterArray = $this->getFilter();
                break;
            case 'delete':
                break;
        }

        return $parameterArray;
    }
}
