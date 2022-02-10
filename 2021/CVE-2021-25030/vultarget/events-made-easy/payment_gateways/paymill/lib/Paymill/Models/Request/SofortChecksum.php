<?php

namespace Paymill\Models\Request;

/**
 * Checksum Model
 *
 * A checksum validation is a simple method to nearly ensure the integrity of transferred data.
 * Basically we generate a hash out of the over given parameters and your private key.
 * If you send us a request with transaction data and the generated checksum, we can easily validate the data
 * because we know your private key and the used hash algorithm.
 * To make the checksum computation as easy as possible we provide this endpoint for you.
 * @tutorial https://paymill.com/de-de/dokumentation/referenz/api-referenz/#document-checksum
 */
class SofortChecksum extends ChecksumBase
{
    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $country;

    /**
     * @var string
     */
    private $clientEmail;

    /**
     * @var array|null
     */
    private $billingAddress;

    /**
     * @var string
     */
    private $clientPhoneNumber;

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return $this
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return $this
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set country ISO 3166-1 alpha-2
     *
     * @param string $country
     *
     * @return $this
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get clientEmail
     *
     * @return string
     */
    public function getClientEmail()
    {
        return $this->clientEmail;
    }

    /**
     * Set clientEmail
     *
     * @param string $clientEmail
     *
     * @return $this
     */
    public function setClientEmail($clientEmail)
    {
        $this->clientEmail = $clientEmail;

        return $this;
    }

    /**
     * Get clientPhoneNumber
     *
     * @return string
     */
    public function getClientPhoneNumber()
    {
        return $this->clientPhoneNumber;
    }

    /**
     * Set clientPhoneNumber
     *
     * @param string $clientPhoneNumber
     *
     * @return $this
     */
    public function setClientPhoneNumber($clientPhoneNumber)
    {
        $this->clientPhoneNumber = $clientPhoneNumber;

        return $this;
    }

    /**
     * Get billing address
     *
     * @return array
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * Set billing address
     *
     * @param array $billingAddress Billing address
     *
     * @return $this
     */
    public function setBillingAddress(array $billingAddress)
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    /**
     * Returns an array of parameters customized for the given method name
     *
     * @param string $method
     *
     * @return array
     */
    public function parameterize($method)
    {
        $parameterArray = parent::parameterize($method);

        if ('create' == $method) {
            if ($this->getFirstName()) {
                $parameterArray['first_name'] = $this->getFirstName();
            }

            if ($this->getLastName()) {
                $parameterArray['last_name'] = $this->getLastName();
            }

            if ($this->getCountry()) {
                $parametersArray['country'] = $this->getCountry();
            }

            if ($this->getClientEmail()) {
                $parametersArray['client_email'] = $this->getClientEmail();
            }

            if ($this->getBillingAddress()) {
                $parametersArray['billing_address'] = $this->getBillingAddress();
            }

            if ($this->getClientPhoneNumber()) {
                $parametersArray['client_phone_number'] = $this->getClientPhoneNumber();
            }
        }

        return $parameterArray;
    }
}
