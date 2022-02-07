<?php

namespace Never5\DownloadMonitor\Dependencies\PayPal\Api;

use Never5\DownloadMonitor\Dependencies\PayPal\Common\PayPalModel;

/**
 * Class RelatedResources
 *
 * Each one representing a financial transaction (Sale, Authorization, Capture, Refund) related to the payment.
 *
 * @package Never5\DownloadMonitor\Dependencies\PayPal\Api
 *
 * @property \Never5\DownloadMonitor\Dependencies\PayPal\Api\Sale sale
 * @property \Never5\DownloadMonitor\Dependencies\PayPal\Api\Authorization authorization
 * @property \Never5\DownloadMonitor\Dependencies\PayPal\Api\Order order
 * @property \Never5\DownloadMonitor\Dependencies\PayPal\Api\Capture capture
 * @property \Never5\DownloadMonitor\Dependencies\PayPal\Api\Refund refund
 */
class RelatedResources extends PayPalModel
{
    /**
     * Sale transaction
     *
     * @param \Never5\DownloadMonitor\Dependencies\PayPal\Api\Sale $sale
     * 
     * @return $this
     */
    public function setSale($sale)
    {
        $this->sale = $sale;
        return $this;
    }

    /**
     * Sale transaction
     *
     * @return \Never5\DownloadMonitor\Dependencies\PayPal\Api\Sale
     */
    public function getSale()
    {
        return $this->sale;
    }

    /**
     * Authorization transaction
     *
     * @param \Never5\DownloadMonitor\Dependencies\PayPal\Api\Authorization $authorization
     * 
     * @return $this
     */
    public function setAuthorization($authorization)
    {
        $this->authorization = $authorization;
        return $this;
    }

    /**
     * Authorization transaction
     *
     * @return \Never5\DownloadMonitor\Dependencies\PayPal\Api\Authorization
     */
    public function getAuthorization()
    {
        return $this->authorization;
    }

    /**
     * Order transaction
     *
     * @param \Never5\DownloadMonitor\Dependencies\PayPal\Api\Order $order
     * 
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Order transaction
     *
     * @return \Never5\DownloadMonitor\Dependencies\PayPal\Api\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Capture transaction
     *
     * @param \Never5\DownloadMonitor\Dependencies\PayPal\Api\Capture $capture
     * 
     * @return $this
     */
    public function setCapture($capture)
    {
        $this->capture = $capture;
        return $this;
    }

    /**
     * Capture transaction
     *
     * @return \Never5\DownloadMonitor\Dependencies\PayPal\Api\Capture
     */
    public function getCapture()
    {
        return $this->capture;
    }

    /**
     * Refund transaction
     *
     * @param \Never5\DownloadMonitor\Dependencies\PayPal\Api\Refund $refund
     * 
     * @return $this
     */
    public function setRefund($refund)
    {
        $this->refund = $refund;
        return $this;
    }

    /**
     * Refund transaction
     *
     * @return \Never5\DownloadMonitor\Dependencies\PayPal\Api\Refund
     */
    public function getRefund()
    {
        return $this->refund;
    }

}
