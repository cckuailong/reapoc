<?php

namespace Never5\DownloadMonitor\Dependencies\PayPal\Api;

/**
 * Class InvoiceAddress
 *
 * Base Address object used as billing address in a payment or extended for Shipping Address.
 *
 * @package Never5\DownloadMonitor\Dependencies\PayPal\Api
 *
 * @property \Never5\DownloadMonitor\Dependencies\PayPal\Api\Phone phone
 */
class InvoiceAddress extends BaseAddress
{
    /**
     * Phone number in E.123 format.
     *
     * @param \Never5\DownloadMonitor\Dependencies\PayPal\Api\Phone $phone
     * 
     * @return $this
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * Phone number in E.123 format.
     *
     * @return \Never5\DownloadMonitor\Dependencies\PayPal\Api\Phone
     */
    public function getPhone()
    {
        return $this->phone;
    }

}
