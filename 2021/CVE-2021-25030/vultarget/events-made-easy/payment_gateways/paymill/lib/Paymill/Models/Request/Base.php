<?php

namespace Paymill\Models\Request;

/**
 * Abstract Model class for request models
 */
abstract class Base
{
    /**
     * Unique identifier of object.
     *
     * @var int
     */
    protected $_id;
    protected $_serviceResource = null;
    protected $_filter;

    /**
     * Converts the model into an array to prepare method calls
     * @param string $method should be used for handling the required parameter
     * @return array
     */
    public abstract function parameterize($method);

    /**
     * Returns the service ressource for this request
     * @return string
     */
    public final function getServiceResource()
    {
        return $this->_serviceResource;
    }

    /**
     * Returns this objects unique identifier
     * @return string identifier
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Sets the unique identifier of this object
     * @param string $id
     * @return \Paymill\Models\Request\Base
     */
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }
    /**
     * Returns the filterArray for getAll
     * @return array
     */
    public function getFilter()
    {
        if (is_null($this->_filter)) {
            return array();
        }
        return $this->_filter;
    }

    /**
     * Sets the filterArray for getAll
     * @param array $filter
     * @return \Paymill\Models\Request\Base
     */
    public function setFilter($filter)
    {
        $this->_filter = $filter;
        return $this;
    }
}
