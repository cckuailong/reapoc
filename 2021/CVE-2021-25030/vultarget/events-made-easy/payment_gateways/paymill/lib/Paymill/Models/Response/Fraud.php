<?php

namespace Paymill\Models\Response;

/**
 * Fraud Model
 * This is an experimental feature! API for frauds may change before being marked as stable for production use.
 */
class Fraud extends Base
{
    /**
     * @var string
     */
    private $_status= null;
    
    /**
     * @var boolean
     */
    private $_livemode;
    
    /**
     * Returns the livemode flag of the fraud
     * @return boolean
     */
    public function getLivemode()
    {
        return $this->_livemode;
    }

    /**
     * Sets the livemode flag of the fraud
     * @param boolean $livemode
     * @return \Paymill\Models\Response\Fraud
     */
    public function setLivemode($livemode)
    {
        $this->_livemode = $livemode;
        return $this;
    }

    /**
     * Returns the status for this fraud
     * @return string||null
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * Sets the status
     * @param string $status
     * @return \Paymill\Models\Response\Fraud
     */
    public function setStatus($status)
    {
        $this->_status = $status;
        return $this;
    }

}
