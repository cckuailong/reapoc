<?php

namespace Never5\DownloadMonitor\Dependencies\PayPal\Api;

use Never5\DownloadMonitor\Dependencies\PayPal\Common\PayPalModel;

/**
 * Class ShippingCost
 *
 * Shipping cost, as a percent or an amount.
 *
 * @package Never5\DownloadMonitor\Dependencies\PayPal\Api
 *
 * @property \Never5\DownloadMonitor\Dependencies\PayPal\Api\Currency amount
 * @property \Never5\DownloadMonitor\Dependencies\PayPal\Api\Tax tax
 */
class ShippingCost extends PayPalModel
{
    /**
     * The shipping cost, as an amount. Valid range is from 0 to 999999.99.
     *
     * @param \Never5\DownloadMonitor\Dependencies\PayPal\Api\Currency $amount
     * 
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * The shipping cost, as an amount. Valid range is from 0 to 999999.99.
     *
     * @return \Never5\DownloadMonitor\Dependencies\PayPal\Api\Currency
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * The tax percentage on the shipping amount.
     *
     * @param \Never5\DownloadMonitor\Dependencies\PayPal\Api\Tax $tax
     * 
     * @return $this
     */
    public function setTax($tax)
    {
        $this->tax = $tax;
        return $this;
    }

    /**
     * The tax percentage on the shipping amount.
     *
     * @return \Never5\DownloadMonitor\Dependencies\PayPal\Api\Tax
     */
    public function getTax()
    {
        return $this->tax;
    }

}
