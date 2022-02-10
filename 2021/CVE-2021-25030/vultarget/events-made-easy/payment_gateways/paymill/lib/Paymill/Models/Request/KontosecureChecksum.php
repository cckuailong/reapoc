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
class KontosecureChecksum extends ChecksumBase
{
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
            if ($this->getCountry()) {
                $parametersArray['country'] = $this->getCountry();
            }

            if ($this->getClientEmail()) {
                $parametersArray['client_email'] = $this->getClientEmail();
            }

            if ($this->getBillingAddress()) {
                $parametersArray['billing_address'] = $this->getBillingAddress();
            }
        }

        return $parameterArray;
    }
}
