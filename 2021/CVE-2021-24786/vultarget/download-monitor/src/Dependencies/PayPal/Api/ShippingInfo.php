<?php

namespace Never5\DownloadMonitor\Dependencies\PayPal\Api;

use Never5\DownloadMonitor\Dependencies\PayPal\Common\PayPalModel;

/**
 * Class ShippingInfo
 *
 * Shipping information for the invoice recipient.
 *
 * @package Never5\DownloadMonitor\Dependencies\PayPal\Api
 *
 * @property string first_name
 * @property string last_name
 * @property string business_name
 * @property \Never5\DownloadMonitor\Dependencies\PayPal\Api\Phone phone
 * @property \Never5\DownloadMonitor\Dependencies\PayPal\Api\InvoiceAddress address
 */
class ShippingInfo extends PayPalModel
{
    /**
     * The invoice recipient first name. Maximum length is 30 characters.
     *
     * @param string $first_name
     * 
     * @return $this
     */
    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;
        return $this;
    }

    /**
     * The invoice recipient first name. Maximum length is 30 characters.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * The invoice recipient last name. Maximum length is 30 characters.
     *
     * @param string $last_name
     * 
     * @return $this
     */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;
        return $this;
    }

    /**
     * The invoice recipient last name. Maximum length is 30 characters.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * The invoice recipient company business name. Maximum length is 100 characters.
     *
     * @param string $business_name
     * 
     * @return $this
     */
    public function setBusinessName($business_name)
    {
        $this->business_name = $business_name;
        return $this;
    }

    /**
     * The invoice recipient company business name. Maximum length is 100 characters.
     *
     * @return string
     */
    public function getBusinessName()
    {
        return $this->business_name;
    }

    /**
     *
     *
     * @param \Never5\DownloadMonitor\Dependencies\PayPal\Api\Phone $phone
     * @return $this
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     *
     *
     * @return \Never5\DownloadMonitor\Dependencies\PayPal\Api\Phone
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @deprecated Not used anymore
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @deprecated Not used anymore
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Address of the invoice recipient.
     *
     * @param \Never5\DownloadMonitor\Dependencies\PayPal\Api\InvoiceAddress $address
     * 
     * @return $this
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * The invoice recipient address.
     *
     * @return \Never5\DownloadMonitor\Dependencies\PayPal\Api\InvoiceAddress
     */
    public function getAddress()
    {
        return $this->address;
    }

}
