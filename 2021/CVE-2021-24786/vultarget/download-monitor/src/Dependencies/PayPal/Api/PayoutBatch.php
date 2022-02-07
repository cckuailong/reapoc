<?php

namespace Never5\DownloadMonitor\Dependencies\PayPal\Api;

use Never5\DownloadMonitor\Dependencies\PayPal\Common\PayPalModel;

/**
 * Class PayoutBatch
 *
 * The PayPal-generated batch status.
 *
 * @package Never5\DownloadMonitor\Dependencies\PayPal\Api
 *
 * @property \Never5\DownloadMonitor\Dependencies\PayPal\Api\PayoutBatchHeader batch_header
 * @property \Never5\DownloadMonitor\Dependencies\PayPal\Api\PayoutItemDetails[] items
 * @property \Never5\DownloadMonitor\Dependencies\PayPal\Api\Links[] links
 */
class PayoutBatch extends PayPalModel
{
    /**
     * A batch header. Includes the generated batch status.
     *
     * @param \Never5\DownloadMonitor\Dependencies\PayPal\Api\PayoutBatchHeader $batch_header
     * 
     * @return $this
     */
    public function setBatchHeader($batch_header)
    {
        $this->batch_header = $batch_header;
        return $this;
    }

    /**
     * A batch header. Includes the generated batch status.
     *
     * @return \Never5\DownloadMonitor\Dependencies\PayPal\Api\PayoutBatchHeader
     */
    public function getBatchHeader()
    {
        return $this->batch_header;
    }

    /**
     * An array of items in a batch payout.
     *
     * @param \Never5\DownloadMonitor\Dependencies\PayPal\Api\PayoutItemDetails[] $items
     * 
     * @return $this
     */
    public function setItems($items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * An array of items in a batch payout.
     *
     * @return \Never5\DownloadMonitor\Dependencies\PayPal\Api\PayoutItemDetails[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Append Items to the list.
     *
     * @param \Never5\DownloadMonitor\Dependencies\PayPal\Api\PayoutItemDetails $payoutItemDetails
     * @return $this
     */
    public function addItem($payoutItemDetails)
    {
        if (!$this->getItems()) {
            return $this->setItems(array($payoutItemDetails));
        } else {
            return $this->setItems(
                array_merge($this->getItems(), array($payoutItemDetails))
            );
        }
    }

    /**
     * Remove Items from the list.
     *
     * @param \Never5\DownloadMonitor\Dependencies\PayPal\Api\PayoutItemDetails $payoutItemDetails
     * @return $this
     */
    public function removeItem($payoutItemDetails)
    {
        return $this->setItems(
            array_diff($this->getItems(), array($payoutItemDetails))
        );
    }


    /**
     * Sets Links
     *
     * @param \Never5\DownloadMonitor\Dependencies\PayPal\Api\Links[] $links
     *
     * @return $this
     */
    public function setLinks($links)
    {
        $this->links = $links;
        return $this;
    }

    /**
     * Gets Links
     *
     * @return \Never5\DownloadMonitor\Dependencies\PayPal\Api\Links[]
     */
    public function getLinks()
    {
        return $this->links;
    }

}
