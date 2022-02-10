<?php

namespace Paymill\Models\Response;

/**
 * Abstract Model class for response models
 */
abstract class Base
{

    /**
     * Unique identifier
     * @var string
     */
    protected $_id;

    /**
     * Unix timestamp of the creation
     * @var integer
     */
    protected $_createdAt;

    /**
     * Unix timestamp of the last update
     * @var integer
     */
    protected $_updatedAt;

    /**
     * Identifier for the App which created this object instance
     * @var string|null
     */
    protected $_appId = null;

    /**
     * Returns the Unique identifier
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Sets the Unique identifier
     * @param string $id
     * @return \Paymill\Models\Response\Base
     */
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    /**
     * Returns the Unix timestamp of the creation
     * @return integer
     */
    public function getCreatedAt()
    {
        return $this->_createdAt;
    }

    /**
     * Sets the Unix timestamp of the creation
     * @param integer $createdAt
     * @return \Paymill\Models\Response\Base
     */
    public function setCreatedAt($createdAt)
    {
        $this->_createdAt = $createdAt;
        return $this;
    }

    /**
     * Returns the Unix timestamp of the last update
     * @return integer
     */
    public function getUpdatedAt()
    {
        return $this->_updatedAt;
    }

    /**
     * Sets the Unix timestamp of the last update
     * @param integer $updatedAt
     * @return \Paymill\Models\Response\Base
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->_updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Returns the identifier of the object which created this object instance
     * @return string|null
     */
    public function getAppId()
    {
        return $this->_appId;
    }

    /**
     * Sets the identifier of the object which created this object instance
     * @param string|null $appId
     * @return \Paymill\Models\Response\Base
     */
    public function setAppId($appId)
    {
        $this->_appId = $appId;
        return $this;
    }

}
