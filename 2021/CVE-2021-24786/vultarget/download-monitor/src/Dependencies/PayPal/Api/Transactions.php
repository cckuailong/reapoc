<?php

namespace Never5\DownloadMonitor\Dependencies\PayPal\Api;

use Never5\DownloadMonitor\Dependencies\PayPal\Common\PayPalModel;

/**
 * Class Transactions
 *
 * 
 *
 * @package Never5\DownloadMonitor\Dependencies\PayPal\Api
 *
 * @property \Never5\DownloadMonitor\Dependencies\PayPal\Api\Amount amount
 */
class Transactions extends PayPalModel
{
    /**
     * Amount being collected.
     * 
     *
     * @param \Never5\DownloadMonitor\Dependencies\PayPal\Api\Amount $amount
     * 
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Amount being collected.
     *
     * @return \Never5\DownloadMonitor\Dependencies\PayPal\Api\Amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

}
