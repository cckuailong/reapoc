<?php

namespace Paymill\Models\Response;

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
     * Either one of the following: (creditcard,debit)
     * @var string
     */
    private $_type;

    /**
     * Id of the client this payment is associated with
     * @var string
     */
    private $_client;

    /**
     * Card type eg. visa, mastercard
     * @var string
     */
    private $_cardType;

    /**
     * @var string
     */
    private $_country;

    /**
     * @var integer
     */
    private $_expireMonth;

    /**
     * @var integer
     */
    private $_expireYear;

    /**
     * Name of the card holder, can be null
     * @var string|null
     */
    private $_cardHolder;

    /**
     * The last four digits of the credit card
     * @var string
     */
    private $_lastFour;

    /**
     * The used Bank Code
     * @var string
     */
    private $_code;

    /**
     * The used account number, for security reasons the number is masked
     * @var string
     */
    private $_account;

    /**
     * The used IBAN - international bank account number
     * @var string
     */
    private $_iban;

    /**
     * The used BIC - bank identifier code
     * @var string
     */
    private $_bic;

    /**
     * Name of the account holder
     * @var string
     */
    private $_holder;

    /**
     * Returns the Type of the Payment (f. ex. creditcard)
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Sets the type of the Payment (f. ex. creditcard)
     * @param string $type
     * @return \Paymill\Models\Response\Payment
     */
    public function setType($type)
    {
        $this->_type = $type;
        return $this;
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
     * @return \Paymill\Models\Response\Payment
     */
    public function setClient($client)
    {
        $this->_client = $client;
        return $this;
    }

    /**
     * Returns the card type
     * @return string
     */
    public function getCardType()
    {
        return $this->_cardType;
    }

    /**
     * Sets the card type
     * @param string $cardType
     * @return \Paymill\Models\Response\Payment
     */
    public function setCardType($cardType)
    {
        $this->_cardType = $cardType;
        return $this;
    }

    /**
     * Returns the country
     * @return string
     */
    public function getCountry()
    {
        return $this->_country;
    }

    /**
     * Sets the country
     * @param string $country
     * @return \Paymill\Models\Response\Payment
     */
    public function setCountry($country)
    {
        $this->_country = $country;
        return $this;
    }

    /**
     * Returns the expiry month
     * @return string
     */
    public function getExpireMonth()
    {
        return $this->_expireMonth;
    }

    /**
     * Sets the expiry month
     * @param string $expireMonth
     * @return \Paymill\Models\Response\Payment
     */
    public function setExpireMonth($expireMonth)
    {
        $this->_expireMonth = $expireMonth;
        return $this;
    }

    /**
     * Returns the expiry year
     * @return string
     */
    public function getExpireYear()
    {
        return $this->_expireYear;
    }

    /**
     * Sets the expiry year
     * @param string $expireYear
     * @return \Paymill\Models\Response\Payment
     */
    public function setExpireYear($expireYear)
    {
        $this->_expireYear = $expireYear;
        return $this;
    }

    /**
     * Returns the card holder name
     * @return string
     */
    public function getCardHolder()
    {
        return $this->_cardHolder;
    }

    /**
     * Sets the card holder name
     * @param string $cardHolder
     * @return \Paymill\Models\Response\Payment
     */
    public function setCardHolder($cardHolder)
    {
        $this->_cardHolder = $cardHolder;
        return $this;
    }

    /**
     * Returns the last four digests of the number (account-/cardnumber)
     * @return string
     */
    public function getLastFour()
    {
        return $this->_lastFour;
    }

    /**
     * Sets the last four digests of the number (account-/cardnumber)
     * @param string $lastFour
     * @return \Paymill\Models\Response\Payment
     */
    public function setLastFour($lastFour)
    {
        $this->_lastFour = $lastFour;
        return $this;
    }

    /**
     * Returns The used Bank Code
     * @return string
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * Sets The used Bank Code
     * @param string $code
     * @return \Paymill\Models\Response\Payment
     */
    public function setCode($code)
    {
        $this->_code = $code;
        return $this;
    }

    /**
     * Returns the used account number, for security reasons the number is masked
     * @return string
     */
    public function getAccount()
    {
        return $this->_account;
    }

    /**
     * Sets the used account number, for security reasons the number is masked
     * @param string $account
     * @return \Paymill\Models\Response\Payment
     */
    public function setAccount($account)
    {
        $this->_account = $account;
        return $this;
    }

    /**
     * Returns the Name of the account holder
     * @return string
     */
    public function getHolder()
    {
        return $this->_holder;
    }

    /**
     * Sets the Name of the account holder
     * @param string $holder
     * @return \Paymill\Models\Response\Payment
     */
    public function setHolder($holder)
    {
        $this->_holder = $holder;
        return $this;
    }

    /**
     * Returns used IBAN
     *
     * @return string
     */
    public function getIban()
    {
        return $this->_iban;
    }

    /**
     * Sets the IBAN
     *
     * @param string $iban
     * @return \Paymill\Models\Response\Payment
     */
    public function setIban($iban)
    {
        $this->_iban = $iban;
        return $this;
    }

    /**
     * Returns used BIC
     *
     * @return string
     */
    public function getBic()
    {
        return $this->_bic;
    }

    /**
     * Sets the BIC
     *
     * @param string $bic
     * @return \Paymill\Models\Response\Payment
     */
    public function setBic($bic)
    {
        $this->_bic = $bic;
        return $this;
    }

}