<?php

namespace Never5\DownloadMonitor\Dependencies\PayPal\Api;

use Never5\DownloadMonitor\Dependencies\PayPal\Common\PayPalModel;

/**
 * Class CreditCardHistory
 *
 * A list of Credit Card Resources
 *
 * @package Never5\DownloadMonitor\Dependencies\PayPal\Api
 *
 * @property \Never5\DownloadMonitor\Dependencies\PayPal\Api\CreditCard[] credit_cards
 * @property int count
 * @property string next_id
 */
class CreditCardHistory extends PayPalModel
{
    /**
     * A list of credit card resources
     *
     *
     * @param \Never5\DownloadMonitor\Dependencies\PayPal\Api\CreditCard[] $credit_cards
     * @return $this
     */
    public function setCreditCards($credit_cards)
    {
        $this->{"credit-cards"} = $credit_cards;
        return $this;
    }

    /**
     * A list of credit card resources
     *
     * @return \Never5\DownloadMonitor\Dependencies\PayPal\Api\CreditCard
     */
    public function getCreditCards()
    {
        return $this->{"credit-cards"};
    }

    /**
     * Number of items returned in each range of results. Note that the last results range could have fewer items than the requested number of items.
     * 
     *
     * @param int $count
     * 
     * @return $this
     */
    public function setCount($count)
    {
        $this->count = $count;
        return $this;
    }

    /**
     * Number of items returned in each range of results. Note that the last results range could have fewer items than the requested number of items.
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Identifier of the next element to get the next range of results.
     * 
     *
     * @param string $next_id
     * 
     * @return $this
     */
    public function setNextId($next_id)
    {
        $this->next_id = $next_id;
        return $this;
    }

    /**
     * Identifier of the next element to get the next range of results.
     *
     * @return string
     */
    public function getNextId()
    {
        return $this->next_id;
    }

}
